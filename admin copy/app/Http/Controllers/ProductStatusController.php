<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

use Throwable;

class ProductStatusController extends Controller
{
    // public function index()
    // {
    //     $statuses = ProductStatus::with('product')->latest()->paginate(10);
    //     return view('products.product_statuses.index', compact('statuses'));
    // }

    /**
 * Display a listing of the resource.
 */
public function index()
{
    $statuses = ProductStatus::with('product')
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    $products = Product::where('is_active', true)->get();
    $templates = ProductStatus::whereNull('product_id')->get();

    return view('products.product_statuses.index', compact('statuses', 'products', 'templates'));
}

    public function create(?string $productUuid = null)
    {
        $product = null;
        if ($productUuid) {
            $product = Product::where('uuid', $productUuid)->firstOrFail();
        }

        // Fetch only templates (product_id = NULL)
        $templates = ProductStatus::whereNull('product_id')->where('is_active', true)->get();

        return view('products.product_statuses.create', compact('product', 'templates'));
    }

    /**
     * Store template OR product status
     */

public function store(Request $request)
{
    Log::info('ProductStatus STORE started', [
        'request' => $request->except('_token')
    ]);

    try {
        $validated = $request->validate([
            'template_id'  => 'nullable|exists:product_statuses,id',
            'name'         => 'nullable|required_without:template_id|string|max:100',
            'product_uuid' => 'nullable|exists:products,uuid',
            'badge_text'   => 'nullable|string|max:20',
            'badge_color'  => 'nullable|string|max:20',
            'description'  => 'nullable|string',
        ]);

        Log::info('ProductStatus validation passed', $validated);

        $productId = null;

        if (!empty($validated['product_uuid'])) {
            $productId = Product::where('uuid', $validated['product_uuid'])->value('id');
            Log::info('Product UUID resolved', [
                'product_uuid' => $validated['product_uuid'],
                'product_id' => $productId,
            ]);
        } else {
            Log::info('No product UUID provided — creating TEMPLATE status');
        }

        $statusModel = new ProductStatus();

        // Clone from template if selected
        if (!empty($validated['template_id'])) {
            $template = ProductStatus::whereNull('product_id')
                ->findOrFail($validated['template_id']);

            $nameToUse = $validated['name'] ?? $template->name;

            $status = ProductStatus::create([
                'product_id'  => $productId,
                'name'        => $nameToUse,
                'badge_text'  => $validated['badge_text'] ?? $template->badge_text,
                'badge_color' => $validated['badge_color'] ?? $template->badge_color,
                'description' => $validated['description'] ?? $template->description,
                'slug'        => $statusModel->generateUniqueSlug($nameToUse, $productId),
            ]);

            Log::info('Status created from template with overrides', [
                'status_id' => $status->id,
                'product_id' => $productId,
                'slug' => $status->slug,
            ]);
        }
        // Create fresh status/template
        else {
            $nameToUse = $validated['name'];

            $status = ProductStatus::create([
                'product_id'  => $productId,
                'name'        => $nameToUse,
                'badge_text'  => $validated['badge_text'] ?? null,
                'badge_color' => $validated['badge_color'] ?? null,
                'description' => $validated['description'] ?? null,
                'slug'        => $statusModel->generateUniqueSlug($nameToUse, $productId),
            ]);

            Log::info('New ProductStatus created', [
                'status_id' => $status->id,
                'product_id' => $productId,
                'slug' => $status->slug,
            ]);
        }

        return back()->with('success', 'Product status saved successfully');

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('ProductStatus validation failed', [
            'errors' => $e->errors(),
        ]);

        return back()
            ->withErrors($e->errors())
            ->withInput();

    } catch (\Throwable $e) {
        Log::error('ProductStatus STORE failed', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return back()
            ->with('error', 'Something went wrong while saving product status.')
            ->withInput();
    }
}

    /**
     * Edit using UUID
     */







public function edit(string $uuid)
{
    try {
        $status = ProductStatus::where('uuid', $uuid)->firstOrFail();
        $product = $status->product;

        // Fetch all template statuses
        $templates = ProductStatus::where('product_id', null)->get();

        return view('products.product_statuses.edit', compact('status', 'product', 'templates'));

    } catch (\Exception $e) {
        return redirect()->route('products.index')
            ->with('error', 'Status not found: ' . $e->getMessage());
    }
}



public function update(Request $request, string $uuid)
{
    $status = ProductStatus::where('uuid', $uuid)->firstOrFail();

    $validated = $request->validate([
        'name'        => [
            'required',
            'string',
            'max:100',
            Rule::unique('product_statuses')
                ->where(fn($q) => $q
                    ->where('product_id', $status->product_id)
                    ->where('slug', Str::slug($request->name))
                )
                ->ignore($status->id),
        ],
        'badge_text'  => 'nullable|string|max:20',
        'badge_color' => 'nullable|string|max:20',
        'description' => 'nullable|string',
        'is_active'   => 'sometimes|boolean',
        'is_template' => 'sometimes|boolean', // virtual field
    ]);

    $status->update([
        'name'        => $validated['name'],
        'slug'        => Str::slug($validated['name']),
        'badge_text'  => $validated['badge_text'] ?? null,
        'badge_color' => $validated['badge_color'] ?? null,
        'description' => $validated['description'] ?? null,
        'is_active'   => $validated['is_active'] ?? false,
    ]);

    // Optional: handle template switch logic temporarily in session or view
    session()->flash('template_switch', $request->has('is_template'));

    return redirect()
        ->route('products.edit', $status->product_id)
        ->with('success', 'Product status updated')
        ->with('activeTab', 'product-status');
}

    public function destroy(string $uuid)
    {
        $status = ProductStatus::where('uuid', $uuid)->firstOrFail();
        $status->delete();

        return back()->with('success', 'Status deleted');
    }


    public function toggleActive(ProductStatus $status)
{
    // Toggle the active status
    $status->is_active = !$status->is_active;
    $status->save();

    return response()->json([
        'ok' => true,
        'id' => $status->id,
        'is_active' => (bool)$status->is_active,
    ]);
}

}
