<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Per-product images page
     * GET /products/{product}/images
     */
    // Per product page
    public function index(Product $product)
    {
        $images = ProductImage::where('product_id', $product->id)->latest()->get();

        $trashedImageCount = ProductImage::onlyTrashed()
            ->where('product_id', $product->id)
            ->count();

        return view('products.products_images.index', compact('product', 'images', 'trashedImageCount'));
    }

    // Global all page
    public function all()
    {
        $images = ProductImage::with(['product' => fn ($q) => $q->withTrashed()])
            ->latest()
            ->paginate(20);

        $trashedImageCount = ProductImage::onlyTrashed()->count();

        // Orphans count (product hard-deleted)
        $orphanCount = ProductImage::whereDoesntHave('product')->count();

        return view('products.products_images.all', compact('images', 'trashedImageCount', 'orphanCount'));
    }

    /**
     * Upload images for a product
     * POST /products/{product}/images
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'images'   => 'required|array',
            'images.*' => 'required|image|max:5120',
        ]);

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_primary' => ($product->images()->count() === 0 && $index === 0),
            ]);
        }

        return back()->with('success', 'Images uploaded successfully');
    }

    /**
     * Soft delete (move to trash) — IMPORTANT: do NOT delete file here
     * DELETE /products/{product}/images/{image}
     */
    // Soft delete (move to trash) - nested
    public function destroy(Product $product, $image)
    {
        $productImage = ProductImage::where('product_id', $product->id)
            ->withTrashed()
            ->findOrFail($image);

        if ($productImage->trashed()) {
            return back()->with('info', 'Image already in trash.');
        }

        // ✅ Soft delete only (do NOT delete file)
        $productImage->delete();

        return back()->with('success', 'Image moved to trash.');
    }


    // ✅ Soft delete globally by image id (works even if product missing)
    public function deleteById($id)
    {
        $img = ProductImage::withTrashed()->findOrFail($id);

        if ($img->trashed()) {
            return back()->with('info', 'Image already in trash.');
        }

        $img->delete();
        return back()->with('success', 'Image moved to trash.');
    }


    /**
     * Set primary image for a product
     * POST /products/{product}/images/{image}/primary
     */
    public function setPrimary(Product $product, $image)
    {
        $productImage = ProductImage::where('product_id', $product->id)
            ->findOrFail($image);

        $product->images()->update(['is_primary' => false]);
        $productImage->update(['is_primary' => true]);

        return back()->with('success', 'Primary image updated');
    }

    /**
     * Global trash page
     * GET /product-images/trash
     */
   // Global trash page
    public function trash()
    {
        $images = ProductImage::onlyTrashed()
            ->with(['product' => fn ($q) => $q->withTrashed()])
            ->latest('deleted_at')
            ->paginate(20);

        $trashedImageCount = ProductImage::onlyTrashed()->count();

        return view('products.products_images.trash', compact('images', 'trashedImageCount'));
    }

    // Restore
    public function restore($id)
    {
        $img = ProductImage::onlyTrashed()->findOrFail($id);
        $img->restore();

        return back()->with('success', 'Image restored successfully.');
    }

    // Force delete: delete file + row
    public function forceDelete($id)
    {
        $img = ProductImage::onlyTrashed()->findOrFail($id);

        try {
            Storage::disk('public')->delete($img->image_path);
            $img->forceDelete();

            return back()->with('success', 'Image permanently deleted.');
        } catch (QueryException $e) {
            Log::error('Image force delete failed', [
                'image_id' => $id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Cannot permanently delete this image.');
        }
    }


    /**
 * Toggle primary image for a product
 * POST /products/{product}/images/{image}/primary-toggle
 */
public function togglePrimary(Product $product, $image)
{
    try {
        // Find the image by product_id and image_id
        $productImage = ProductImage::where('product_id', $product->id)
            ->findOrFail($image);

        // Set all other images to non-primary
        $product->images()->update(['is_primary' => false]);

        // If this image is not primary, set it as primary
        if (!$productImage->is_primary) {
            $productImage->update(['is_primary' => true]);
        }

        return response()->json([
            'ok' => true,
            'id' => $productImage->id,
            'is_primary' => $productImage->is_primary
        ]);
    } catch (Exception $e) {
        Log::error('Error while toggling primary image', [
            'error' => $e->getMessage(),
            'product_id' => $product->id,
            'image_id' => $image,
        ]);
        return response()->json(['ok' => false, 'message' => 'Error occurred while updating primary image.']);
    }
}




}
