<?php

namespace App\Services\AI;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductBatchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Report\Concerns\ReportFilters;
use App\Http\Controllers\Report\FinancialDashboardController;
use App\Http\Controllers\Report\FinancialTodayDashboardController;
use App\Models\AiAssistantLog;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\AccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * File 1 of the AI Assistant feature: this is the boundary that decides
 * which "knowledge" of the store Gemini is allowed to touch, and how.
 *
 * Every capability Gemini can use is declared once in toolDefinitions() --
 * a name, a JSON schema for its arguments, the access_key it requires, and
 * a handler. availableToolsFor() then filters that full list down to only
 * the tools the CURRENT user is actually permitted to use (via the same
 * AccessService::canAccessKey() check every manual admin page already goes
 * through) before it's ever sent to Gemini -- so the assistant can never
 * see, let alone call, a tool the logged-in user couldn't use by hand.
 *
 * Read tools run immediately. Write tools (create_product, create_category)
 * never execute on the model's say-so alone: the loop stops and hands a
 * "confirm_required" result back to the browser, and only resolvePending()
 * -- triggered by a human clicking Confirm -- actually runs them.
 *
 * Every completed turn is written to ai_assistant_logs (see
 * App\Models\AiAssistantLog) as the permanent audit trail; the live,
 * turn-by-turn conversation itself lives in the session instead, since it's
 * disposable state, not an accountability record.
 */
class GeminiAssistantService
{
    use ReportFilters;

    private const MAX_TOOL_ROUNDS = 6;

    /**
     * Curated, not the full model list Gemini offers -- every one of these
     * was verified live to support generateContent + function calling.
     * Speed labels come from timed live calls with this app's real tool
     * schema: the "-lite" models answered in ~1-1.5s, the plain "flash"
     * models took 15-25s (they reason/think heavily with no observed way to
     * cap it through this API).
     */
    public const AVAILABLE_MODELS = [
        'gemini-flash-lite-latest' => 'Fastest — recommended (~1s, always the newest lite model)',
        'gemini-2.5-flash-lite' => 'Fast (~1s)',
        'gemini-3.1-flash-lite' => 'Fast (~1s)',
        'gemini-3.5-flash-lite' => 'Fast (~1s)',
        'gemini-flash-latest' => 'Slower, more capable (~15-25s, always the newest flash model)',
        'gemini-3.5-flash' => 'Slower, more capable (~15-25s)',
        'gemini-3.6-flash' => 'Slower, more capable (untested speed)',
        'gemini-3.7-flash' => 'Slower, more capable (untested speed)',
    ];

    private const PERIODS = [
        'today', 'yesterday', 'this_week', 'last_week',
        'this_month', 'last_month', 'this_year', 'last_year',
    ];

    public function __construct(private AccessService $accessService)
    {
    }

    // ---------------------------------------------------------------
    // Public entry points
    // ---------------------------------------------------------------

    public function sendMessage(User $user, string $message): array
    {
        $history = session($this->historyKey($user), []);
        $history[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        return $this->runLoop($user, $history, $message, [], []);
    }

    public function resolvePending(User $user, string $decision): array
    {
        $pending = session($this->pendingKey($user));

        if (!$pending) {
            return ['type' => 'error', 'text' => 'There is nothing waiting for confirmation.'];
        }

        $history = session($this->historyKey($user), []);
        $tools = $this->toolDefinitions();
        $tool = $tools[$pending['name']] ?? null;

        if ($decision === 'confirm' && $tool && $this->accessService->canAccessKey($user, $tool['access_key'])) {
            $result = $tool['handler']($pending['args'], $user);
        } elseif ($decision === 'confirm') {
            $result = ['success' => false, 'message' => 'Permission was revoked before this could be confirmed.'];
        } else {
            $result = ['success' => false, 'message' => 'The user declined to confirm this action.'];
        }

        // Re-use the model's original part (preserved when the confirmation was
        // first requested) rather than rebuilding one, so any thoughtSignature
        // Gemini attached to it survives the round trip -- see the identical
        // note in runLoop().
        $echoedPart = $pending['part'] ?? ['functionCall' => ['name' => $pending['name'], 'args' => $pending['args']]];
        $echoedPart['functionCall']['args'] = $this->asJsonObject($pending['args']);

        $history[] = ['role' => 'model', 'parts' => [$echoedPart]];
        $history[] = ['role' => 'user', 'parts' => [['functionResponse' => ['name' => $pending['name'], 'response' => $this->asJsonObject($result)]]]];

        session()->forget($this->pendingKey($user));

        $this->logTurn(
            $user,
            $pending['original_prompt'] ?? '(confirmation)',
            $pending['tool_calls_so_far'] ?? [[ 'name' => $pending['name'], 'args' => $pending['args']]],
            array_merge($pending['tool_results_so_far'] ?? [], [$result]),
            null,
            true,
            (bool) ($result['success'] ?? false),
            $decision === 'confirm' ? 'completed' : 'cancelled'
        );

        return $this->runLoop($user, $history, $pending['original_prompt'] ?? null, [], [], continued: true);
    }

    public function pendingConfirmation(User $user): ?array
    {
        $pending = session($this->pendingKey($user));
        if (!$pending) {
            return null;
        }

        $tool = $this->toolDefinitions()[$pending['name']] ?? null;
        if (!$tool) {
            return null;
        }

        return [
            'tool' => $pending['name'],
            'args' => $pending['args'],
            'summary' => $tool['confirm_summary']($pending['args']),
        ];
    }

    public function historyFor(User $user): array
    {
        return session($this->historyKey($user), []);
    }

    public function clearConversation(User $user): void
    {
        session()->forget([$this->historyKey($user), $this->pendingKey($user)]);
    }

    /**
     * The admin-chosen model (Setting) wins over .env's GEMINI_MODEL, which
     * only serves as the initial default before anyone has picked one.
     */
    public function currentModel(): string
    {
        return \App\Models\Setting::get('gemini_model') ?: (string) config('services.gemini.model');
    }

    /**
     * Every per-conversation session key MUST be scoped by user id.
     * session()->regenerate() (used throughout the app's login flow) only
     * rotates the session ID -- it does not clear the session's existing
     * data array (that's what invalidate() does) -- so a bare, unscoped key
     * here would let one user's chat history/pending action survive into
     * whichever user logs in next on the same browser. Scoping by id makes
     * that leak impossible regardless of what the login flow does or
     * doesn't clear.
     */
    private function historyKey(User $user): string
    {
        return "ai_chat.{$user->id}.history";
    }

    private function pendingKey(User $user): string
    {
        return "ai_chat.{$user->id}.pending";
    }

    // ---------------------------------------------------------------
    // Core agentic loop
    // ---------------------------------------------------------------

    private function runLoop(
        User $user,
        array $history,
        ?string $originalPrompt,
        array $toolCalls,
        array $toolResults,
        bool $continued = false
    ): array {
        $allowedTools = $this->availableToolsFor($user);
        $wasWrite = false;
        $wasDenied = false;

        for ($round = 0; $round < self::MAX_TOOL_ROUNDS; $round++) {
            $content = $this->callGemini($history, $allowedTools);
            $parts = $content['parts'] ?? [];

            $functionCallParts = array_values(array_filter($parts, fn ($p) => isset($p['functionCall'])));

            if (empty($functionCallParts)) {
                $text = trim(implode("\n", array_filter(array_map(
                    fn ($p) => $p['text'] ?? null,
                    $parts
                )))) ?: 'I could not generate a response for that.';

                $history[] = ['role' => 'model', 'parts' => [['text' => $text]]];
                session([$this->historyKey($user) => $history]);
                session()->forget($this->pendingKey($user));

                if ($originalPrompt !== null && !$continued) {
                    $this->logTurn($user, $originalPrompt, $toolCalls, $toolResults, $text, $wasWrite, !$wasDenied, 'completed');
                } elseif ($continued) {
                    // Confirmation turn already logged its own row in resolvePending();
                    // this just appends the assistant's follow-up text to that context.
                }

                return ['type' => 'message', 'text' => $text];
            }

            // Gemini supports calling several tools in one turn ("parallel
            // function calling"). If every one of them is a plain read, run
            // them all and answer in a single batched turn -- Gemini expects
            // one functionResponse per functionCall it sent, together. A
            // write tool still needs a human's confirm click, so any batch
            // containing one falls back to handling just its first call
            // (write flows are effectively always asked one at a time).
            $allReadOnly = true;
            foreach ($functionCallParts as $part) {
                $toolName = $part['functionCall']['name'] ?? '';
                $toolDef = $allowedTools[$toolName] ?? null;
                if (!$toolDef || $toolDef['is_write']) {
                    $allReadOnly = false;
                    break;
                }
            }

            if ($allReadOnly && count($functionCallParts) > 1) {
                $modelParts = [];
                $responseParts = [];

                foreach ($functionCallParts as $part) {
                    $call = $part['functionCall'];
                    $name = $call['name'];
                    $args = $call['args'] ?? [];
                    $tool = $allowedTools[$name];

                    $toolCalls[] = ['name' => $name, 'args' => $args];
                    $result = $tool['handler']($args, $user);
                    $toolResults[] = $result;

                    $echoedPart = $part;
                    $echoedPart['functionCall']['args'] = $this->asJsonObject($args);
                    $modelParts[] = $echoedPart;
                    $responseParts[] = ['functionResponse' => ['name' => $name, 'response' => $this->asJsonObject($result)]];
                }

                $history[] = ['role' => 'model', 'parts' => $modelParts];
                $history[] = ['role' => 'user', 'parts' => $responseParts];
                continue;
            }

            $functionCallPart = $functionCallParts[0];
            $call = $functionCallPart['functionCall'];
            $name = $call['name'] ?? '';
            $args = $call['args'] ?? [];
            $tool = $allowedTools[$name] ?? null;

            $toolCalls[] = ['name' => $name, 'args' => $args];

            // Echo back the model's OWN part, not a reconstruction of it: Gemini
            // attaches a thoughtSignature alongside functionCall for models with
            // thinking enabled, and it must be replayed unchanged on the next
            // request or the API rejects the call ("missing a thought_signature").
            // Only the args sub-field needs normalizing (see asJsonObject()).
            $echoedPart = $functionCallPart;
            $echoedPart['functionCall']['args'] = $this->asJsonObject($args);

            if (!$tool) {
                $wasDenied = true;
                $result = ['success' => false, 'message' => "You do not have permission to use \"{$name}\", or it does not exist."];
                $toolResults[] = $result;
                $history[] = ['role' => 'model', 'parts' => [$echoedPart]];
                $history[] = ['role' => 'user', 'parts' => [['functionResponse' => ['name' => $name, 'response' => $this->asJsonObject($result)]]]];
                continue;
            }

            if ($tool['is_write']) {
                $wasWrite = true;

                session([
                    $this->historyKey($user) => $history,
                    $this->pendingKey($user) => [
                        'name' => $name,
                        'args' => $args,
                        'part' => $echoedPart,
                        'original_prompt' => $originalPrompt,
                        'tool_calls_so_far' => $toolCalls,
                        'tool_results_so_far' => $toolResults,
                    ],
                ]);

                if (!$continued) {
                    $this->logTurn($user, $originalPrompt ?? '(continued)', $toolCalls, $toolResults, null, true, true, 'pending_confirmation');
                }

                return [
                    'type' => 'confirm_required',
                    'tool' => $name,
                    'args' => $args,
                    'summary' => $tool['confirm_summary']($args),
                ];
            }

            $result = $tool['handler']($args, $user);
            $toolResults[] = $result;
            $history[] = ['role' => 'model', 'parts' => [$echoedPart]];
            $history[] = ['role' => 'user', 'parts' => [['functionResponse' => ['name' => $name, 'response' => $this->asJsonObject($result)]]]];
        }

        session([$this->historyKey($user) => $history]);
        $fallback = "I wasn't able to finish that within a reasonable number of steps — try rephrasing or asking something narrower.";

        if ($originalPrompt !== null) {
            $this->logTurn($user, $originalPrompt, $toolCalls, $toolResults, $fallback, $wasWrite, !$wasDenied, 'error');
        }

        return ['type' => 'message', 'text' => $fallback];
    }

    // ---------------------------------------------------------------
    // Gemini HTTP client
    // ---------------------------------------------------------------

    private function callGemini(array $contents, array $allowedTools): array
    {
        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            throw new RuntimeException('The Gemini API key has not been configured. Add GEMINI_API_KEY to your .env file.');
        }

        $model = $this->currentModel();
        $baseUrl = rtrim((string) config('services.gemini.base_url'), '/');

        $body = [
            'system_instruction' => ['parts' => [['text' => $this->systemInstruction()]]],
            'contents' => $contents,
        ];

        if (!empty($allowedTools)) {
            $body['tools'] = [['function_declarations' => $this->toGeminiDeclarations($allowedTools)]];
        }

        // Comfortably above the ~1-1.5s a "-lite" model actually takes, but
        // well under the controller's own set_time_limit() so a genuinely
        // stuck call fails here (a clean error to the user) instead of
        // hitting PHP's hard execution-time limit (a fatal error/504).
        $response = Http::timeout(20)
            ->withHeaders(['x-goog-api-key' => $apiKey])
            ->post("{$baseUrl}/models/{$model}:generateContent", $body);

        if ($response->failed()) {
            Log::error('Gemini API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $message = $response->json('error.message') ?? "HTTP {$response->status()}";
            throw new RuntimeException("The AI service returned an error: {$message}");
        }

        $candidate = $response->json('candidates.0');

        if (!$candidate) {
            $blockReason = $response->json('promptFeedback.blockReason');
            throw new RuntimeException($blockReason
                ? "The request was blocked by Gemini's safety filters ({$blockReason})."
                : 'The AI service returned an empty response.');
        }

        return $candidate['content'] ?? ['parts' => []];
    }

    private function systemInstruction(): string
    {
        return "You are the AI assistant built into this store's admin panel. "
            . "Answer questions about sales, profit, margins, and products strictly using the tools you're given -- "
            . "never invent numbers, and always call a tool before stating a figure. All money values are in the "
            . "store's base currency. If a tool result says an action isn't permitted, tell the user plainly that "
            . "they don't have access to it rather than trying to work around it. Keep answers short and concrete, "
            . "quoting the real numbers a tool returned.\n\n"
            . "When creating something (a product, a category, a stock batch), never invent field values the user "
            . "didn't give you. Collect them conversationally, asking for ONE missing field at a time in a natural "
            . "order (e.g. for a product: name, then barcode, then category, then brand) rather than listing every "
            . "field at once. Only call the create_* tool once you have every required field. After a product is "
            . "created successfully, ask whether they'd like to add stock for it now, and if so walk through "
            . "create_product_batch's fields the same way, one at a time (location, quantity, unit, buy price, sell "
            . "price). If the user explicitly says to skip a field, invent, or use a default, then you may do so.\n\n"
            . "For advisory questions -- what to restock, what new product to add, what's trending -- ground every "
            . "claim in a tool result (get_category_performance, get_restock_recommendations, get_trending_products, "
            . "etc.) and phrase the answer as a suggestion based on the data, not a certainty. Never invent a product "
            . "or category that didn't come from a tool result.";
    }

    private function toGeminiDeclarations(array $tools): array
    {
        $declarations = [];

        foreach ($tools as $name => $tool) {
            $parameters = $tool['parameters'];

            // A tool with no arguments (e.g. list_locations) declares an
            // empty properties map. Same PHP array/object ambiguity as
            // asJsonObject() above: an empty PHP array always re-encodes as
            // JSON "[]", but Gemini's schema requires "properties" to be a
            // map/object -- "Cannot bind a list to map for field 'properties'".
            if (isset($parameters['properties'])) {
                $parameters['properties'] = $this->asJsonObject($parameters['properties']);
            }

            $declarations[] = [
                'name' => $name,
                'description' => $tool['description'],
                'parameters' => $parameters,
            ];
        }

        return $declarations;
    }

    /**
     * PHP's json_decode(..., true) collapses JSON "{}" and "[]" into the same
     * empty PHP array, and json_encode([]) always turns back into "[]" -- so
     * an empty tool-call args/response, once round-tripped, silently becomes
     * a JSON list where Gemini's proto schema expects a single object. That
     * mismatch is exactly what "Proto field is not repeating, cannot start
     * list" means. Force empty arrays back into a genuine JSON object here.
     */
    private function asJsonObject(mixed $value): mixed
    {
        return (is_array($value) && empty($value)) ? new \stdClass() : $value;
    }

    private function availableToolsFor(User $user): array
    {
        return array_filter(
            $this->toolDefinitions(),
            fn (array $tool) => $this->accessService->canAccessKey($user, $tool['access_key'])
        );
    }

    // ---------------------------------------------------------------
    // Tool registry -- the "knowledge" boundary
    // ---------------------------------------------------------------

    private function toolDefinitions(): array
    {
        $periodProperty = [
            'type' => 'STRING',
            'enum' => self::PERIODS,
            'description' => 'Which period to look at.',
        ];

        return [
            'get_financial_summary' => [
                'access_key' => 'dashboard',
                'is_write' => false,
                'description' => 'Get total sales, profit, margin, expenses and refunds for a period. Use this for '
                    . 'any question about revenue, profit, margin, today\'s sales, or expenses.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => ['period' => $periodProperty],
                ],
                'handler' => fn (array $args) => $this->toolGetFinancialSummary($args),
            ],

            'get_top_selling_products' => [
                'access_key' => 'dashboard',
                'is_write' => false,
                'description' => 'List the best-performing products for a period, ranked by quantity sold, revenue, '
                    . 'or profit. Use this for "which products are selling" / "top products" / "best performers" '
                    . '/ "hype products" questions.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'period' => $periodProperty,
                        'limit' => ['type' => 'INTEGER', 'description' => 'Max products to return, default 5.'],
                        'sort_by' => [
                            'type' => 'STRING',
                            'enum' => ['revenue', 'profit', 'quantity', 'margin'],
                            'description' => 'What to rank by. Defaults to revenue.',
                        ],
                    ],
                ],
                'handler' => fn (array $args) => $this->toolGetTopSellingProducts($args),
            ],

            'get_trending_products' => [
                'access_key' => 'dashboard',
                'is_write' => false,
                'description' => 'Find products whose sales are accelerating -- this week vs last week -- for '
                    . '"trending" / "hype" / "what\'s hot right now" questions. Different from top-selling: a '
                    . 'trending product may have modest total volume but a sharp recent increase.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'limit' => ['type' => 'INTEGER', 'description' => 'Max products to return, default 5.'],
                    ],
                ],
                'handler' => fn (array $args) => $this->toolGetTrendingProducts($args),
            ],

            'get_category_performance' => [
                'access_key' => 'dashboard',
                'is_write' => false,
                'description' => 'Break down revenue, quantity, and profit by product category for a period. Use '
                    . 'this to reason about which categories are strong or underperforming -- a starting point for '
                    . '"what new product should I add" questions (answer advisorily, based on which categories are '
                    . 'already doing well vs thin, never as a guaranteed fact).',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => ['period' => $periodProperty],
                ],
                'handler' => fn (array $args) => $this->toolGetCategoryPerformance($args),
            ],

            'get_low_stock_products' => [
                'access_key' => 'stock',
                'is_write' => false,
                'description' => 'List products that are low on stock (or out of stock) right now, optionally at '
                    . 'one location. Use this for "what\'s running low" / "stock fulfillment" / "shortage" questions.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'location_name' => ['type' => 'STRING', 'description' => 'Optional location filter.'],
                        'threshold' => ['type' => 'INTEGER', 'description' => 'Consider "low" below this quantity. Defaults to 10.'],
                    ],
                ],
                'handler' => fn (array $args) => $this->toolGetLowStockProducts($args),
            ],

            'get_restock_recommendations' => [
                'access_key' => 'stock',
                'is_write' => false,
                'description' => 'Recommend which products to reorder/buy for the store, by cross-referencing what\'s '
                    . 'low on stock against what actually sells well. Use this for "what should I buy for the store" '
                    . '/ "what needs restocking most" questions -- prioritizes fast movers that are also low, over '
                    . 'slow movers that happen to be low.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => ['period' => $periodProperty],
                ],
                'handler' => fn (array $args) => $this->toolGetRestockRecommendations($args),
            ],

            'get_payment_summary' => [
                'access_key' => 'dashboard',
                'is_write' => false,
                'description' => 'Get payment method breakdown (cash/card/mobile etc.), total collected, and '
                    . 'outstanding customer dues for a period. Use this for "bill", "payment", "how much is unpaid" '
                    . 'questions.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => ['period' => $periodProperty],
                ],
                'handler' => fn (array $args) => $this->toolGetPaymentSummary($args),
            ],

            'search_orders' => [
                'access_key' => 'orders',
                'is_write' => false,
                'description' => 'Search orders by order number or customer name.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'query' => ['type' => 'STRING', 'description' => 'Order number or customer name to search for.'],
                        'period' => $periodProperty,
                    ],
                    'required' => ['query'],
                ],
                'handler' => fn (array $args) => $this->toolSearchOrders($args),
            ],

            'get_order_details' => [
                'access_key' => 'orders',
                'is_write' => false,
                'description' => 'Get the full bill for one order: line items, totals, payment status, amount paid '
                    . 'and due, and payment history. Use this when the user names a specific order number.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'order_no' => ['type' => 'STRING', 'description' => 'The order number.'],
                    ],
                    'required' => ['order_no'],
                ],
                'handler' => fn (array $args) => $this->toolGetOrderDetails($args),
            ],

            'search_customers' => [
                'access_key' => 'customers',
                'is_write' => false,
                'description' => 'Search customers by name or phone number, showing their due balance, advance '
                    . 'balance and reward points.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'query' => ['type' => 'STRING', 'description' => 'Customer name or phone to search for.'],
                    ],
                    'required' => ['query'],
                ],
                'handler' => fn (array $args) => $this->toolSearchCustomers($args),
            ],

            'get_product_sales' => [
                'access_key' => 'orders',
                'is_write' => false,
                'description' => 'Look up how many units of one specific product were ordered/sold, and its revenue, '
                    . 'in a given period. Use this when the user names a specific product.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'product_name' => ['type' => 'STRING', 'description' => 'Product name or part of it.'],
                        'period' => $periodProperty,
                    ],
                    'required' => ['product_name'],
                ],
                'handler' => fn (array $args) => $this->toolGetProductSales($args),
            ],

            'list_products' => [
                'access_key' => 'products',
                'is_write' => false,
                'description' => 'Search existing products by name, to check what already exists.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'search' => ['type' => 'STRING', 'description' => 'Optional name filter.'],
                    ],
                ],
                'handler' => fn (array $args) => $this->toolListProducts($args),
            ],

            'list_categories' => [
                'access_key' => 'categories',
                'is_write' => false,
                'description' => 'List existing product categories, optionally filtered by name.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'search' => ['type' => 'STRING', 'description' => 'Optional name filter.'],
                    ],
                ],
                'handler' => fn (array $args) => $this->toolListCategories($args),
            ],

            'list_locations' => [
                'access_key' => 'locations',
                'is_write' => false,
                'description' => 'List the store\'s locations, needed before adding a stock batch (a batch is added at one location).',
                'parameters' => ['type' => 'OBJECT', 'properties' => []],
                'handler' => fn (array $args) => $this->toolListLocations(),
            ],

            'create_product' => [
                'access_key' => 'products',
                'is_write' => true,
                'description' => 'Create a new product (name, barcode, category, brand, description). Ask the user '
                    . 'for each missing field one at a time instead of inventing values -- only barcode may be '
                    . 'auto-generated if the user explicitly says they don\'t have one. This does not set a price or '
                    . 'stock quantity -- offer to add an initial batch with create_product_batch right after.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'name' => ['type' => 'STRING', 'description' => 'Product name.'],
                        'barcode' => ['type' => 'STRING', 'description' => 'Unique barcode/SKU.'],
                        'category_name' => ['type' => 'STRING', 'description' => 'Category name; created if it does not already exist.'],
                        'brand_name' => ['type' => 'STRING', 'description' => 'Brand name; created if it does not already exist.'],
                        'description' => ['type' => 'STRING'],
                    ],
                    'required' => ['name', 'barcode'],
                ],
                'confirm_summary' => fn (array $args) => sprintf(
                    'Create product "%s" (barcode %s)%s%s.',
                    $args['name'] ?? '',
                    $args['barcode'] ?? '',
                    !empty($args['category_name']) ? ", category \"{$args['category_name']}\"" : '',
                    !empty($args['brand_name']) ? ", brand \"{$args['brand_name']}\"" : ''
                ),
                'handler' => fn (array $args, User $user) => $this->toolCreateProduct($args, $user),
            ],

            'create_category' => [
                'access_key' => 'categories',
                'is_write' => true,
                'description' => 'Create a new product category.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'name' => ['type' => 'STRING', 'description' => 'Category name.'],
                    ],
                    'required' => ['name'],
                ],
                'confirm_summary' => fn (array $args) => sprintf('Create category "%s".', $args['name'] ?? ''),
                'handler' => fn (array $args, User $user) => $this->toolCreateCategory($args, $user),
            ],

            'create_product_batch' => [
                'access_key' => 'product_batches',
                'is_write' => true,
                'description' => 'Add a stock batch (quantity + price) to an existing product at a location. The '
                    . 'product must already exist -- use list_products/create_product first. Ask for each missing '
                    . 'field one at a time: product, location, quantity, unit, buy price, sell price, and optionally '
                    . 'a batch number and expiry date.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'product_name' => ['type' => 'STRING', 'description' => 'Name of the existing product to add stock to.'],
                        'location_name' => ['type' => 'STRING', 'description' => 'Name of the store location to stock this batch at.'],
                        'batch_no' => ['type' => 'STRING', 'description' => 'Optional batch/lot number.'],
                        'quantity' => ['type' => 'NUMBER', 'description' => 'Quantity received.'],
                        'unit' => ['type' => 'STRING', 'enum' => ['pcs', 'dozen', 'box', 'kg', 'g', 'l', 'ml']],
                        'buy_price' => ['type' => 'NUMBER', 'description' => 'Cost price per unit.'],
                        'sell_price' => ['type' => 'NUMBER', 'description' => 'Selling price per unit.'],
                        'expiry_date' => ['type' => 'STRING', 'description' => 'Optional expiry date, YYYY-MM-DD.'],
                    ],
                    'required' => ['product_name', 'location_name', 'quantity', 'unit', 'buy_price', 'sell_price'],
                ],
                'confirm_summary' => fn (array $args) => sprintf(
                    'Add batch to "%s" at "%s": %s %s @ buy %s / sell %s%s.',
                    $args['product_name'] ?? '',
                    $args['location_name'] ?? '',
                    $args['quantity'] ?? '',
                    $args['unit'] ?? '',
                    $args['buy_price'] ?? '',
                    $args['sell_price'] ?? '',
                    !empty($args['batch_no']) ? ", batch #{$args['batch_no']}" : ''
                ),
                'handler' => fn (array $args, User $user) => $this->toolCreateProductBatch($args, $user),
            ],
        ];
    }

    // ---------------------------------------------------------------
    // Tool handlers -- each one delegates to the SAME controller/query
    // logic the manual admin UI already uses, so the assistant can never
    // compute a number (or create a record) a different way than a human
    // clicking through the app would.
    // ---------------------------------------------------------------

    private function toolGetFinancialSummary(array $args): array
    {
        $period = in_array($args['period'] ?? null, self::PERIODS, true) ? $args['period'] : 'today';

        if ($period === 'today') {
            $response = app(FinancialTodayDashboardController::class)->data(Request::create('/', 'GET'));
            $data = $response->getData(true)['dashboard'] ?? [];
        } else {
            $response = app(FinancialDashboardController::class)->metrics(Request::create('/', 'GET', ['date_range' => $period]));
            $data = $response->getData(true)['metrics'] ?? [];
        }

        return [
            'period' => $period,
            'total_sales' => $data['total_sales'] ?? 0,
            'total_orders' => $data['total_orders'] ?? 0,
            'net_sales' => $data['net_sales'] ?? 0,
            'cost_of_goods_sold' => $data['cost_of_goods_sold'] ?? 0,
            'gross_profit' => $data['gross_profit'] ?? 0,
            'gross_margin_percent' => $data['gross_margin'] ?? 0,
            'expenses_total' => $data['expenses_total'] ?? 0,
            'net_profit' => $data['net_profit'] ?? 0,
            'net_profit_margin_percent' => $data['profit_margin'] ?? 0,
            'total_refunds' => $data['total_refunds'] ?? 0,
            'total_returns' => $data['total_returns'] ?? 0,
        ];
    }

    private function toolGetTopSellingProducts(array $args): array
    {
        $period = in_array($args['period'] ?? null, self::PERIODS, true) ? $args['period'] : 'this_month';
        $limit = max(1, min((int) ($args['limit'] ?? 5), 20));
        $sortBy = in_array($args['sort_by'] ?? null, ['revenue', 'profit', 'quantity', 'margin'], true)
            ? $args['sort_by'] : 'revenue';

        $top = $this->rawTopProducts($period);
        $sortKey = ['revenue' => 'total_revenue', 'profit' => 'profit', 'quantity' => 'total_qty', 'margin' => 'margin'][$sortBy];
        usort($top, fn ($a, $b) => ($b[$sortKey] ?? 0) <=> ($a[$sortKey] ?? 0));

        return ['period' => $period, 'sorted_by' => $sortBy, 'products' => array_slice($top, 0, $limit)];
    }

    /**
     * The raw top_products list for a period, unsliced and unsorted --
     * shared by get_top_selling_products, get_trending_products, and
     * get_restock_recommendations so all three read the exact same
     * already-audited revenue/profit numbers instead of three separate
     * computations that could drift apart.
     */
    private function rawTopProducts(string $period): array
    {
        if ($period === 'today') {
            $response = app(FinancialTodayDashboardController::class)->data(Request::create('/', 'GET'));
            return $response->getData(true)['dashboard']['top_products'] ?? [];
        }

        $response = app(FinancialDashboardController::class)->tables(Request::create('/', 'GET', ['date_range' => $period]));
        return $response->getData(true)['tables']['top_products'] ?? [];
    }

    private function toolGetProductSales(array $args): array
    {
        $name = trim((string) ($args['product_name'] ?? ''));
        if ($name === '') {
            return ['found' => false, 'message' => 'No product name was given.'];
        }

        $product = Product::where('name', 'like', '%' . $name . '%')->first();
        if (!$product) {
            return ['found' => false, 'message' => "No product matching \"{$name}\" was found."];
        }

        $period = in_array($args['period'] ?? null, self::PERIODS, true) ? $args['period'] : 'this_month';
        [$start, $end] = $this->resolveDateRange($period, null, null);

        $matchingItems = fn ($q) => $q
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->where('oi.product_id', $product->id)
            ->whereBetween('o.created_at', [$start, $end])
            ->tap(fn ($qq) => $this->excludeInvalidStatuses($qq))
            ->tap(fn ($qq) => $this->excludeSplitChildren($qq));

        $row = $matchingItems(DB::table('order_items as oi'))
            ->selectRaw('
                COUNT(DISTINCT o.id) as order_count,
                COALESCE(SUM(GREATEST(oi.quantity - oi.returned_qty, 0)), 0) as net_qty_sold,
                COALESCE(SUM(oi.total_price), 0) as gross_revenue
            ')
            ->first();

        // oi.total_price stays at its original, as-charged amount even after a
        // return reduces returned_qty -- net it against the same return_items
        // rows the trusted dashboards use, or "revenue" would overstate a
        // fully/partially returned line the same way this session's earlier
        // profit-calculation bugs did.
        $refunded = (float) DB::table('return_items')
            ->whereIn('order_item_id', function ($q) use ($matchingItems) {
                $matchingItems($q->from('order_items as oi'))->select('oi.id');
            })
            ->sum('refund_amount');

        $netRevenue = max(0, (float) ($row->gross_revenue ?? 0) - $refunded);

        return [
            'found' => true,
            'product' => $product->name,
            'period' => $period,
            'order_count' => (int) ($row->order_count ?? 0),
            'net_qty_sold' => (float) ($row->net_qty_sold ?? 0),
            'revenue' => $netRevenue,
            'refunded_amount' => $refunded,
        ];
    }

    private function toolListProducts(array $args): array
    {
        $query = Product::query()->with('category:id,name')->latest()->limit(10);

        if (!empty($args['search'])) {
            $query->where('name', 'like', '%' . $args['search'] . '%');
        }

        return [
            'products' => $query->get(['id', 'name', 'barcode', 'category_id', 'is_active'])
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'barcode' => $p->barcode,
                    'category' => $p->category->name ?? null,
                    'is_active' => (bool) $p->is_active,
                ])->all(),
        ];
    }

    private function toolListCategories(array $args): array
    {
        $query = Category::query()->latest()->limit(15);

        if (!empty($args['search'])) {
            $query->where('name', 'like', '%' . $args['search'] . '%');
        }

        return [
            'categories' => $query->get(['id', 'name'])
                ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->all(),
        ];
    }

    private function toolListLocations(): array
    {
        return [
            'locations' => Location::where('is_active', true)->orderBy('name')
                ->get(['id', 'name', 'code'])
                ->map(fn ($l) => ['id' => $l->id, 'name' => $l->name, 'code' => $l->code])->all(),
        ];
    }

    private function toolGetTrendingProducts(array $args): array
    {
        $limit = max(1, min((int) ($args['limit'] ?? 5), 20));

        $current = $this->rawTopProducts('this_week');
        $previousByProductId = [];
        foreach ($this->rawTopProducts('last_week') as $row) {
            $id = $row['product']['id'] ?? null;
            if ($id !== null) {
                $previousByProductId[$id] = (float) ($row['total_qty'] ?? 0);
            }
        }

        $trending = [];
        foreach ($current as $row) {
            $id = $row['product']['id'] ?? null;
            $currentQty = (float) ($row['total_qty'] ?? 0);
            $previousQty = $previousByProductId[$id] ?? 0.0;
            $increase = $currentQty - $previousQty;

            if ($increase <= 0) {
                continue;
            }

            $trending[] = [
                'product' => $row['product']['name'] ?? 'Unknown',
                'this_week_qty' => $currentQty,
                'last_week_qty' => $previousQty,
                'qty_increase' => $increase,
                'growth_percent' => $previousQty > 0 ? round(($increase / $previousQty) * 100, 1) : null,
            ];
        }

        usort($trending, fn ($a, $b) => $b['qty_increase'] <=> $a['qty_increase']);

        return ['products' => array_slice($trending, 0, $limit)];
    }

    private function toolGetCategoryPerformance(array $args): array
    {
        $period = in_array($args['period'] ?? null, self::PERIODS, true) ? $args['period'] : 'this_month';
        [$start, $end] = $this->resolveDateRange($period, null, null);

        $rows = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->leftJoin('product_batches as pb', 'oi.product_batch_id', '=', 'pb.id')
            ->whereBetween('o.created_at', [$start, $end])
            ->tap(fn ($q) => $this->excludeInvalidStatuses($q))
            ->tap(fn ($q) => $this->excludeSplitChildren($q))
            ->selectRaw('
                COALESCE(c.name, "Uncategorized") as category,
                COALESCE(SUM(GREATEST(oi.quantity - oi.returned_qty, 0)), 0) as net_qty,
                COALESCE(SUM(oi.total_price), 0) as gross_revenue,
                COALESCE(SUM(GREATEST(oi.quantity - oi.returned_qty, 0) * pb.buy_price), 0) as cost
            ')
            ->groupBy('category')
            ->get();

        // Same as get_product_sales: oi.total_price stays at the as-charged
        // amount after a return, so it has to be netted against the actual
        // refunded amount or "revenue" overstates a category with returns.
        $refundsByCategory = DB::table('return_items as ri')
            ->join('order_items as oi', 'ri.order_item_id', '=', 'oi.id')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->whereBetween('o.created_at', [$start, $end])
            ->tap(fn ($q) => $this->excludeInvalidStatuses($q))
            ->tap(fn ($q) => $this->excludeSplitChildren($q))
            ->selectRaw('COALESCE(c.name, "Uncategorized") as category, COALESCE(SUM(ri.refund_amount), 0) as refunded')
            ->groupBy('category')
            ->pluck('refunded', 'category');

        $categories = $rows->map(function ($r) use ($refundsByCategory) {
            $netRevenue = max(0, (float) $r->gross_revenue - (float) ($refundsByCategory[$r->category] ?? 0));
            $cost = (float) $r->cost;
            $profit = round($netRevenue - $cost, 2);

            return [
                'category' => $r->category,
                'net_qty_sold' => (float) $r->net_qty,
                'revenue' => round($netRevenue, 2),
                'profit' => $profit,
                'margin_percent' => $netRevenue > 0 ? round(($profit / $netRevenue) * 100, 2) : 0,
            ];
        })->sortByDesc('revenue')->values()->all();

        return ['period' => $period, 'categories' => $categories];
    }

    private function toolGetLowStockProducts(array $args): array
    {
        $threshold = max(1, (int) ($args['threshold'] ?? 10));

        $query = DB::table('batch_stocks as bs')
            ->join('product_batches as pb', 'bs.product_batch_id', '=', 'pb.id')
            ->join('products as p', 'pb.product_id', '=', 'p.id')
            ->join('locations as l', 'bs.location_id', '=', 'l.id')
            ->whereNull('pb.deleted_at')
            ->where('pb.is_active', true)
            ->whereRaw('(bs.on_hand - bs.reserved) < ?', [$threshold]);

        if (!empty($args['location_name'])) {
            $query->where('l.name', 'like', '%' . $args['location_name'] . '%');
        }

        $rows = $query
            ->selectRaw('p.name as product, l.name as location, (bs.on_hand - bs.reserved) as available, bs.on_hand, bs.reserved')
            ->orderBy('available')
            ->limit(25)
            ->get();

        return [
            'threshold' => $threshold,
            'items' => $rows->map(fn ($r) => [
                'product' => $r->product,
                'location' => $r->location,
                'available' => (float) $r->available,
                'on_hand' => (float) $r->on_hand,
                'reserved' => (float) $r->reserved,
            ])->all(),
        ];
    }

    private function toolGetRestockRecommendations(array $args): array
    {
        $period = in_array($args['period'] ?? null, self::PERIODS, true) ? $args['period'] : 'this_month';

        $lowStock = $this->toolGetLowStockProducts(['threshold' => 10]);

        $velocityByProduct = [];
        foreach ($this->rawTopProducts($period) as $row) {
            $name = $row['product']['name'] ?? null;
            if ($name !== null) {
                $velocityByProduct[$name] = (float) ($row['total_qty'] ?? 0);
            }
        }

        $recommendations = array_map(fn ($item) => [
            'product' => $item['product'],
            'location' => $item['location'],
            'available' => $item['available'],
            'recent_sales_velocity' => $velocityByProduct[$item['product']] ?? 0.0,
        ], $lowStock['items']);

        usort($recommendations, fn ($a, $b) => $b['recent_sales_velocity'] <=> $a['recent_sales_velocity']);

        return ['period' => $period, 'recommendations' => array_slice($recommendations, 0, 15)];
    }

    private function toolGetPaymentSummary(array $args): array
    {
        $period = in_array($args['period'] ?? null, self::PERIODS, true) ? $args['period'] : 'today';

        if ($period === 'today') {
            $response = app(FinancialTodayDashboardController::class)->data(Request::create('/', 'GET'));
            $data = $response->getData(true)['dashboard'] ?? [];
        } else {
            $response = app(FinancialDashboardController::class)->metrics(Request::create('/', 'GET', ['date_range' => $period]));
            $data = $response->getData(true)['metrics'] ?? [];
        }

        return [
            'period' => $period,
            'total_collected' => $data['paid_amount'] ?? ($data['total_payments'] ?? 0),
            'payment_methods' => $data['payment_methods_breakdown'] ?? [],
            'amount_due_this_period' => $data['due_amount'] ?? 0,
            'average_customer_due_balance' => $data['avg_due_balance'] ?? 0,
        ];
    }

    private function toolSearchOrders(array $args): array
    {
        $query = trim((string) ($args['query'] ?? ''));
        if ($query === '') {
            return ['orders' => []];
        }

        $period = in_array($args['period'] ?? null, self::PERIODS, true) ? $args['period'] : 'this_month';
        [$start, $end] = $this->resolveDateRange($period, null, null);

        $rows = DB::table('orders as o')
            ->leftJoin('customers as c', 'o.customer_id', '=', 'c.id')
            ->whereBetween('o.created_at', [$start, $end])
            ->tap(fn ($q) => $this->excludeSplitChildren($q))
            ->where(function ($q) use ($query) {
                $q->where('o.order_no', 'like', '%' . $query . '%')
                    ->orWhere('c.name', 'like', '%' . $query . '%');
            })
            ->selectRaw('o.order_no, o.status, o.payment_status, o.payable_total, o.paid_total, o.due_total, o.created_at, COALESCE(c.name, "Guest") as customer_name')
            ->orderByDesc('o.created_at')
            ->limit(10)
            ->get();

        return [
            'orders' => $rows->map(fn ($r) => [
                'order_no' => $r->order_no,
                'customer' => $r->customer_name,
                'status' => $r->status,
                'payment_status' => $r->payment_status,
                'payable_total' => (float) $r->payable_total,
                'paid_total' => (float) $r->paid_total,
                'due_total' => (float) $r->due_total,
                'created_at' => (string) $r->created_at,
            ])->all(),
        ];
    }

    private function toolGetOrderDetails(array $args): array
    {
        $orderNo = trim((string) ($args['order_no'] ?? ''));
        if ($orderNo === '') {
            return ['found' => false, 'message' => 'No order number was given.'];
        }

        $order = Order::with(['items.product:id,name', 'payments', 'customer:id,name,phone'])
            ->where('order_no', $orderNo)
            ->first();

        if (!$order) {
            return ['found' => false, 'message' => "No order found with number \"{$orderNo}\"."];
        }

        return [
            'found' => true,
            'order_no' => $order->order_no,
            'customer' => $order->customer->name ?? 'Guest',
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'subtotal' => (float) $order->subtotal,
            'discount_total' => (float) $order->discount_total,
            'payable_total' => (float) $order->payable_total,
            'paid_total' => (float) $order->paid_total,
            'due_total' => (float) $order->due_total,
            'items' => $order->items->map(fn ($i) => [
                'product' => $i->product->name ?? 'Unknown',
                'quantity' => (float) $i->quantity,
                'returned_qty' => (float) $i->returned_qty,
                'total_price' => (float) $i->total_price,
            ])->all(),
            'payments' => $order->payments->map(fn ($p) => [
                'method' => $p->method,
                'amount' => (float) $p->amount,
                'status' => $p->status,
                'date' => optional($p->created_at)->toDateString(),
            ])->all(),
        ];
    }

    private function toolSearchCustomers(array $args): array
    {
        $query = trim((string) ($args['query'] ?? ''));
        if ($query === '') {
            return ['customers' => []];
        }

        $customers = Customer::where('name', 'like', '%' . $query . '%')
            ->orWhere('phone', 'like', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'name', 'phone', 'due_balance', 'advance_balance', 'reward_points', 'is_active']);

        return [
            'customers' => $customers->map(fn ($c) => [
                'name' => $c->name,
                'phone' => $c->phone,
                'due_balance' => (float) $c->due_balance,
                'advance_balance' => (float) $c->advance_balance,
                'reward_points' => (float) $c->reward_points,
                'is_active' => (bool) $c->is_active,
            ])->all(),
        ];
    }

    private function toolCreateProduct(array $args, User $user): array
    {
        return $this->dispatchToController(ProductController::class, 'store', [
            'name' => $args['name'] ?? null,
            'barcode' => $args['barcode'] ?? null,
            'category_name' => $args['category_name'] ?? null,
            'brand_name' => $args['brand_name'] ?? null,
            'description' => $args['description'] ?? null,
            'is_active' => true,
        ], $user);
    }

    private function toolCreateCategory(array $args, User $user): array
    {
        return $this->dispatchToController(CategoryController::class, 'store', [
            'name' => $args['name'] ?? null,
        ], $user);
    }

    private function toolCreateProductBatch(array $args, User $user): array
    {
        $product = Product::where('name', 'like', '%' . ($args['product_name'] ?? '') . '%')->first();
        if (!$product) {
            return ['success' => false, 'message' => "No product matching \"{$args['product_name']}\" was found. Create it first."];
        }

        $location = Location::where('name', 'like', '%' . ($args['location_name'] ?? '') . '%')->first();
        if (!$location) {
            return ['success' => false, 'message' => "No location matching \"{$args['location_name']}\" was found."];
        }

        return $this->dispatchToController(ProductBatchController::class, 'store', [
            'product_id' => $product->id,
            'location_id' => $location->id,
            'batch_no' => $args['batch_no'] ?? null,
            'quantity' => $args['quantity'] ?? null,
            'unit' => $args['unit'] ?? null,
            'buy_price' => $args['buy_price'] ?? null,
            'original_sell_price' => $args['sell_price'] ?? null,
            'expiry_date' => $args['expiry_date'] ?? null,
        ], $user);
    }

    /**
     * Runs an existing admin controller's write action in-process, so the
     * assistant reuses its exact validation/creation logic instead of a
     * second, hand-rolled copy that could quietly drift out of sync.
     *
     * These controllers respond via redirect()->with('success'|'error', ...)
     * or ->withErrors(...), which normally becomes the NEXT page's flash
     * banner. Since this call isn't a real HTTP redirect, that flash data
     * would otherwise leak onto the admin's next unrelated page load -- so
     * the surrounding session state is snapshotted and restored afterward.
     */
    private function dispatchToController(string $controllerClass, string $method, array $input, User $user): array
    {
        $request = Request::create('/', 'POST', array_filter($input, fn ($v) => $v !== null && $v !== ''));
        $request->setUserResolver(fn () => $user);

        $before = [
            'success' => session('success'),
            'error' => session('error'),
            'errors' => session('errors'),
        ];

        try {
            app($controllerClass)->{$method}($request);
        } catch (Throwable $e) {
            Log::error('AI assistant tool dispatch failed', [
                'controller' => $controllerClass,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => 'Failed: ' . $e->getMessage()];
        } finally {
            $after = [
                'success' => session('success'),
                'error' => session('error'),
                'errors' => session('errors'),
            ];

            session()->forget(['success', 'error', 'errors']);
            foreach ($before as $key => $value) {
                if ($value !== null) {
                    session()->put($key, $value);
                }
            }
        }

        if (!empty($after['errors']) && $after['errors']->any()) {
            return ['success' => false, 'message' => implode(' ', $after['errors']->all())];
        }

        if (!empty($after['error'])) {
            return ['success' => false, 'message' => $after['error']];
        }

        return ['success' => true, 'message' => $after['success'] ?? 'Done.'];
    }

    // ---------------------------------------------------------------
    // Audit log (File 2)
    // ---------------------------------------------------------------

    private function logTurn(
        User $user,
        string $prompt,
        array $toolCalls,
        array $toolResults,
        ?string $responseText,
        bool $wasWrite,
        bool $wasAllowed,
        string $status
    ): void {
        try {
            AiAssistantLog::create([
                'user_id' => $user->id,
                'prompt' => $prompt,
                'tool_calls' => $toolCalls,
                'tool_results' => $toolResults,
                'response_text' => $responseText,
                'was_write_action' => $wasWrite,
                'was_allowed' => $wasAllowed,
                'status' => $status,
                'ip_address' => request()?->ip(),
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to write AI assistant log', ['error' => $e->getMessage()]);
        }
    }
}
