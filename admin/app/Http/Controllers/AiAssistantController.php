<?php

namespace App\Http\Controllers;

use App\Models\AiAccessRequest;
use App\Models\AiAssistantLog;
use App\Models\Setting;
use App\Models\User;
use App\Services\AccessService;
use App\Services\AI\GeminiAssistantService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class AiAssistantController extends Controller
{
    public function __construct(
        private GeminiAssistantService $assistant,
        private AccessService $accessService
    ) {
    }

    /**
     * Reachable by any authenticated user regardless of the ai_assistant
     * access key -- someone without it still needs to land here to see the
     * "request access" screen instead of a bare 403. chat()/resolve()/
     * reset()/logs() stay gated the normal way.
     */
    public function index(Request $request)
    {
        return view('ai_assistant.index', $this->pageData($request));
    }

    /**
     * Same payload the full page renders with, as JSON -- powers the
     * floating quick-access modal on every other page without duplicating
     * this logic.
     */
    public function state(Request $request)
    {
        $data = $this->pageData($request);

        return response()->json([
            'has_access' => $data['hasAiAccess'],
            'request_status' => $data['requestStatus'],
            'has_key' => $data['hasKey'],
            'history' => $data['history'],
            'pending' => $data['pending'],
        ]);
    }

    public function requestAccess(Request $request)
    {
        $user = $request->user();

        if ($this->accessService->canAccessKey($user, 'ai_assistant')) {
            return response()->json(['status' => 'already_granted', 'message' => 'You already have access.']);
        }

        $existing = AiAccessRequest::where('user_id', $user->id)->where('status', 'pending')->first();
        if ($existing) {
            return response()->json(['status' => 'pending', 'message' => 'Your request is already waiting for an admin.']);
        }

        AiAccessRequest::create(['user_id' => $user->id, 'status' => 'pending']);

        return response()->json(['status' => 'pending', 'message' => 'Request sent. An admin needs to approve it on the Access Keys page.']);
    }

    public function chat(Request $request)
    {
        $validated = $request->validate(['message' => 'required|string|max:2000']);

        if (!config('services.gemini.key')) {
            return response()->json([
                'type' => 'error',
                'text' => 'The Gemini API key has not been configured yet. Add GEMINI_API_KEY to the .env file.',
            ], 422);
        }

        // A multi-round tool-calling turn can take longer than PHP's default
        // 30s max_execution_time -- without this, a slow round gets killed
        // mid-request with a raw fatal error instead of the JSON error below.
        @set_time_limit(60);

        try {
            $result = $this->assistant->sendMessage($request->user(), $validated['message']);

            return response()->json($result);
        } catch (Throwable $e) {
            return response()->json(['type' => 'error', 'text' => $e->getMessage()], 500);
        }
    }

    public function resolve(Request $request)
    {
        $validated = $request->validate(['decision' => 'required|in:confirm,cancel']);

        @set_time_limit(60);

        try {
            $result = $this->assistant->resolvePending($request->user(), $validated['decision']);

            return response()->json($result);
        } catch (Throwable $e) {
            return response()->json(['type' => 'error', 'text' => $e->getMessage()], 500);
        }
    }

    public function reset(Request $request)
    {
        $this->assistant->clearConversation($request->user());

        return response()->json(['type' => 'ok']);
    }

    /**
     * Changing the model is a shared, site-wide setting (like currency or
     * store name), not a per-user preference -- gated by the stricter
     * 'settings' key on top of the route's own 'ai_assistant' gate.
     */
    public function updateModel(Request $request)
    {
        if (!$this->accessService->canAccessKey($request->user(), 'settings')) {
            abort(403);
        }

        $validated = $request->validate([
            'model' => ['required', 'string', Rule::in(array_keys(GeminiAssistantService::AVAILABLE_MODELS))],
        ]);

        Setting::setMany(['gemini_model' => $validated['model']]);

        return response()->json(['status' => 'ok', 'model' => $validated['model']]);
    }

    /**
     * Every user with plain 'ai_assistant' access could otherwise reach this
     * page and see every OTHER user's prompts and tool activity -- an
     * unrestricted cross-user history leak. Only users who also hold 'rbac'
     * (the same key that already gates the Access Keys / audit-log pages)
     * get the full, cross-user view with the user filter; everyone else is
     * hard-scoped to their own rows regardless of what they put in the
     * query string.
     */
    public function logs(Request $request)
    {
        $user = $request->user();
        $canViewAll = $this->accessService->canAccessKey($user, 'rbac');

        $logs = AiAssistantLog::with('user:id,name,email')
            ->when(!$canViewAll, fn ($q) => $q->where('user_id', $user->id))
            ->when($canViewAll && $request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->get('status')))
            ->when($request->filled('search'), fn ($q) => $q->where('prompt', 'like', '%' . $request->get('search') . '%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $users = $canViewAll ? User::orderBy('name')->get(['id', 'name']) : collect();

        return view('ai_assistant.logs', compact('logs', 'users', 'canViewAll'));
    }

    private function pageData(Request $request): array
    {
        $user = $request->user();
        $hasAiAccess = $this->accessService->canAccessKey($user, 'ai_assistant');

        $requestStatus = null;
        if (!$hasAiAccess) {
            $requestStatus = AiAccessRequest::where('user_id', $user->id)->latest()->value('status');
        }

        return [
            'history' => $hasAiAccess ? $this->assistant->historyFor($user) : [],
            'pending' => $hasAiAccess ? $this->assistant->pendingConfirmation($user) : null,
            'hasKey' => (bool) config('services.gemini.key'),
            'hasAiAccess' => $hasAiAccess,
            'requestStatus' => $requestStatus,
            'currentModel' => $this->assistant->currentModel(),
            'availableModels' => GeminiAssistantService::AVAILABLE_MODELS,
            'canManageModel' => $this->accessService->canAccessKey($user, 'settings'),
        ];
    }
}
