<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;
use Illuminate\Support\Facades\Log;
use Throwable;

class CategoryController extends Controller
{
    // Option 1: Basic pagination
    public function index()
    {
        Log::info('Category index loaded with pagination');

        // Simple pagination
        $categories = Category::with('parent')
            ->latest()
            ->paginate(10); // 10 items per page

        return view('categories.index', compact('categories'));
    }
    // Option 2: With search/filter
    public function indexWithSearch(Request $request)
    {
        Log::info('Category index loaded with filters', $request->all());

        $categories = Category::with('parent')
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->parent_id, function ($query, $parentId) {
                return $query->where('parent_id', $parentId);
            })
            ->latest()
            ->paginate($request->per_page ?? 10)
            ->withQueryString(); // Preserve all query parameters

        $parentCategories = Category::whereNull('parent_id')->get();

        return view('categories.index', compact('categories', 'parentCategories'));
    }

    // Option 3: AJAX pagination (for infinite scroll/load more)
    public function indexAjax(Request $request)
    {
        if ($request->ajax()) {
            $categories = Category::with('parent')
                ->latest()
                ->paginate(10);

            return response()->json([
                'html' => view('categories.partials.category-list', compact('categories'))->render(),
                'nextPageUrl' => $categories->nextPageUrl(),
                'hasMorePages' => $categories->hasMorePages()
            ]);
        }

        // Regular request
        $categories = Category::with('parent')
            ->latest()
            ->paginate(10);

        return view('categories.index', compact('categories'));
    }


    public function ajaxSearch(Request $request)
    {
        $search = trim($request->q);

        if ($search === '') {
            return response()->json([]);
        }

        // 1️⃣ Find matching parents FIRST
        $parentIds = Category::whereNull('parent_id')
            ->where('name', 'LIKE', "%{$search}%")
            ->pluck('id');

        // 2️⃣ Fetch:
        // - matched categories
        // - children of matched parents
        // - categories whose parent name matches
        $categories = Category::with('parent')
            ->where(function ($query) use ($search, $parentIds) {
                $query
                    ->where('name', 'LIKE', "%{$search}%")               // direct match
                    ->orWhereIn('parent_id', $parentIds)                // children of matched parent
                    ->orWhereHas('parent', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");       // parent name match
                    });
            })
            ->orderByRaw('COALESCE(parent_id, id)') // parent first
            ->orderBy('parent_id')
            ->orderBy('name')
            ->limit(50) // ⚡ fast response
            ->get();

        return response()->json($categories);
    }


    public function create()
    {
        $categories = Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->get();

        // Flatten tree into searchable paths
        $flat = [];

        $walk = function ($cats, $path = []) use (&$walk, &$flat) {
            foreach ($cats as $cat) {
                $currentPath = [...$path, $cat->name];

                $flat[] = [
                    'id' => $cat->id,
                    'path' => implode(' → ', $currentPath),
                    'search' => strtolower(implode(' ', $currentPath)),
                ];

                if ($cat->childrenRecursive->count()) {
                    $walk($cat->childrenRecursive, $currentPath);
                }
            }
        };

        $walk($categories);

        return view('categories.create', compact('flat'));
    }



    public function store(Request $request)
    {
        Log::info('Category store started', $request->except('image'));

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:50|unique:categories,barcode',
            // Refined image rule: ensures it's an image of allowed types and max 10MB
            'image' => 'nullable|image|mimetypes:image/jpeg,image/png,image/webp|max:10240',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            // This returns precise errors (e.g., size/type mismatch)
            return back()->withErrors($validator)->withInput();
        }

        try {
            // UUID, Barcode, SVG logic...
            $uuid = (string) Str::uuid();
            Log::info('UUID generated', ['uuid' => $uuid]);

            $barcode = $request->barcode ?: 'CAT-' . strtoupper(Str::random(8));
            Log::info('Barcode resolved', ['barcode' => $barcode]);

            $dns = new DNS1D();
            $svg = $dns->getBarcodeSVG($barcode, 'C128');
            $barcodePath = "category-barcodes/{$barcode}.svg";
            Storage::disk('public')->put($barcodePath, $svg);
            Log::info('Barcode SVG stored', ['path' => $barcodePath]);

            // Image upload
            $imagePath = null;

            if ($request->hasFile('image')) {

                if (!$request->file('image')->isValid()) {
                    $errorCode = $request->file('image')->getError();
                    Log::error('Image upload failed (Server/PHP error)', ['php_error_code' => $errorCode]);

                    // THIS IS THE VALUE WE NEED TO SEE:
                    return back()->withErrors("Image upload failed (Server Error). PHP Error Code: {$errorCode}. Please check server limits/permissions.");
                }

                // CRITICAL FIX: Check for server upload failure (happens BEFORE Laravel processing)
                // This step is crucial for diagnosing PHP limits or server permissions.
                if (!$request->file('image')->isValid()) {
                    $errorCode = $request->file('image')->getError();
                    Log::error('Image upload failed (Server/PHP error)', ['php_error_code' => $errorCode]);

                    // Returning the error code to the user for immediate diagnosis
                    return back()->withErrors("Image upload failed (Server Error). PHP Error Code: {$errorCode}. Please check server limits/permissions.");
                }

                $imagePath = $request->file('image')->store('category-images', 'public');
                Log::info('Category image uploaded', ['path' => $imagePath]);
            }

            // Save category
            $category = new Category();
            $category->uuid = $uuid;
            $category->name = $request->name;
            $category->barcode = $barcode;
            $category->barcode_svg = $barcodePath;
            $category->image = $imagePath;
            $category->parent_id = $request->parent_id;
            $category->is_active = true;
            $category->saveOrFail();

            Log::info('Category saved successfully', ['id' => $category->id]);

            return redirect()->route('categories.index')->with('success', 'Category Created Successfully');
        } catch (Throwable $e) {
            Log::error('Category store failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->withErrors('Category creation failed. Error: ' . $e->getMessage());
        }
    }
    public function edit(Category $category)
    {
        Log::info('Category edit loaded', ['id' => $category->id]);

        // Load all root categories excluding the current one
        $parents = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->with('childrenRecursive')
            ->get();

        // Flatten tree into searchable paths for search-select input
        $flat = [];

        $walk = function ($cats, $path = []) use (&$walk, &$flat, $category) {
            foreach ($cats as $cat) {
                // Skip the current category itself to prevent cyclic parent
                if ($cat->id == $category->id) continue;

                $currentPath = [...$path, $cat->name];

                $flat[] = [
                    'id' => $cat->id,
                    'path' => implode(' → ', $currentPath),
                    'search' => strtolower(implode(' ', $currentPath)),
                ];

                if ($cat->childrenRecursive->count()) {
                    $walk($cat->childrenRecursive, $currentPath);
                }
            }
        };

        $walk($parents);

        return view('categories.edit', compact('category', 'parents', 'flat'));
    }

    public function update(Request $request, Category $category)
    {
        Log::info('Category update started', ['id' => $category->id]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:50|unique:categories,barcode,' . $category->id,
            'image' => 'nullable|image|max:5120',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Update barcode if changed
            if ($request->barcode && $request->barcode !== $category->barcode) {
                if ($category->barcode_svg) {
                    Storage::disk('public')->delete($category->barcode_svg);
                }
                $dns = new DNS1D();
                $svg = $dns->getBarcodeSVG($request->barcode, 'C128');
                $barcodePath = "category-barcodes/{$request->barcode}.svg";
                Storage::disk('public')->put($barcodePath, $svg);

                $category->barcode = $request->barcode;
                $category->barcode_svg = $barcodePath;
                Log::info('Barcode updated', ['barcode' => $request->barcode]);
            }

            // Update image if uploaded - REFACTORED BLOCK
            if ($request->hasFile('image')) {
                if (!$request->file('image')->isValid()) {
                    Log::error('Image upload failed', ['error' => $request->file('image')->getError()]);
                    return back()->withErrors('Image upload failed. Check file size/type.');
                }

                // 1. Delete old image if it exists
                if ($category->image) {
                    // Note: Your original controller used Storage::disk('public')->delete(),
                    // which is safer and explicit. Let's use that.
                    Storage::disk('public')->delete($category->image);
                    Log::info('Old category image deleted', ['path' => $category->image]);
                }

                // 2. Store new image and save path
                $category->image = $request->file('image')->store('category-images', 'public');
                Log::info('Category image updated', ['path' => $category->image]);
            }
            // END OF REFACTORED BLOCK



            $category->name = $request->name;
            $category->parent_id = $request->parent_id;
            $category->saveOrFail();

            Log::info('Category updated successfully', ['id' => $category->id]);
            return redirect()->route('categories.index')->with('success', 'Category Updated Successfully');
        } catch (Throwable $e) {
            Log::error('Category update failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->withErrors('Category update failed. Error: ' . $e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        Log::info('Category delete started', ['id' => $category->id]);

        if ($category->barcode_svg) {
            Storage::disk('public')->delete($category->barcode_svg);
            Log::info('Barcode SVG deleted', ['path' => $category->barcode_svg]);
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
            Log::info('Category image deleted', ['path' => $category->image]);
        }

        $category->delete();
        Log::info('Category deleted', ['id' => $category->id]);

        return back()->with('success', 'Category Deleted Successfully');
    }

    public function bulkDestroy(Request $request)
{
    $ids = $request->input('ids');

    if (empty($ids)) {
        return redirect()->back()->with('error', 'No categories selected.');
    }

    // This will delete all selected categories
    Category::whereIn('id', $ids)->delete();

    return redirect()->route('categories.index')->with('success', 'Selected categories deleted successfully.');
}
}
