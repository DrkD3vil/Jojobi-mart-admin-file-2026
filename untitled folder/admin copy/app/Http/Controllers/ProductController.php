<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductImage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // <-- Add this
use Illuminate\Support\Facades\DB; // For transactions
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{



    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(5, min(100, $perPage));

        $products = Product::query()
            ->with(['category', 'brand', 'images'])
            ->with(['batches' => function ($q) {
                $q->select([
                    'id',
                    'product_id',
                    'batch_no',
                    'expiry_date',
                    'unit',
                    'is_active',
                    'is_online',
                    'is_offline',
                    'is_pos',
                    'original_sell_price',
                    'sell_price',
                    'discounted_price',
                    'discount_percentage',
                ])
                    // ✅ Ledger-driven stock quantity per batch:
                    ->addSelect([
                        'stock_qty' => DB::table('stock_ledgers as sl')
                            ->selectRaw("
                        COALESCE(SUM(
                            CASE
                                WHEN sl.direction = 'IN' THEN sl.qty
                                WHEN sl.direction = 'OUT' THEN -sl.qty
                                ELSE 0
                            END
                        ), 0)
                    ")
                            ->whereColumn('sl.product_batch_id', 'product_batches.id')
                    ]);
            }])
            ->latest()
            ->paginate($perPage);

        $categoriesCount = Category::count();
        $totalImages = ProductImage::count();
        $trashedCount = \App\Models\Product::onlyTrashed()->count();

        $products_batchs = ProductBatch::query()
            ->with(['product' => function ($q) {
                $q->select(['id', 'name', 'barcode']);
            }])
            ->get();



        return view('products.index', compact('products', 'categoriesCount', 'totalImages', 'trashedCount', 'products_batchs'));
    }


    // In ProductController@create()
    public function create()
    {
        Log::info('ProductController@create - Start creating new product form');

        try {
            Log::debug('Fetching active categories');
            $categories = Category::where('is_active', true)->orderBy('name')->get();
            Log::debug('Fetched ' . $categories->count() . ' active categories');

            Log::debug('Fetching active brands');
            $brands = Brand::where('is_active', true)->orderBy('name')->get();
            Log::debug('Fetched ' . $brands->count() . ' active brands');

            Log::info('ProductController@create - Successfully loaded create form data');
            return view('products.create', compact('categories', 'brands'));
        } catch (\Exception $e) {
            Log::error('ProductController@create - Error loading create form: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('products.index')
                ->with('error', 'Failed to load product creation form. Please try again.');
        }
    }




    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        Log::info('ProductController@store - Start storing new product', [
            'request_data' => $request->except('_token'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            Log::debug('Validating product data');

            // Prepare validation rules
            $validationRules = [
                'barcode' => 'required|string|max:50|unique:products,barcode',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'note' => 'nullable|string',
                'is_active' => 'boolean'
            ];

            // Only validate category_id if it's provided (not empty string)
            if ($request->filled('category_id')) {
                $validationRules['category_id'] = 'exists:categories,id';
            }

            // Only validate brand_id if it's provided (not empty string)
            if ($request->filled('brand_id')) {
                $validationRules['brand_id'] = 'exists:brands,id';
            }

            // Add validation for category_name if provided
            if ($request->filled('category_name') && !$request->filled('category_id')) {
                $validationRules['category_name'] = 'string|max:255';
            }

            // Add validation for brand_name if provided
            if ($request->filled('brand_name') && !$request->filled('brand_id')) {
                $validationRules['brand_name'] = 'string|max:255';
            }

            $validated = $request->validate($validationRules);
            Log::debug('Validation passed', ['validated_data' => $validated]);

            DB::beginTransaction();
            Log::debug('Database transaction started');

            // Generate UUID for product
            $uuid = (string) \Illuminate\Support\Str::uuid();
            Log::debug('Product Creation Started', ['uuid' => $uuid]);

            // 1. Handle Category
            $categoryId = null;
            if (!empty($request->category_id)) {
                Log::debug('Using existing category_id from dropdown', ['category_id' => $request->category_id]);
                $categoryId = $request->category_id;
            } elseif (!empty($request->category_name)) {
                $categoryName = trim($request->category_name);
                Log::info('Attempting to resolve category', ['name' => $categoryName]);

                // Use firstOrCreate to avoid duplicates
                $category = Category::firstOrCreate(
                    ['name' => $categoryName],
                    [
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        'is_active' => true,
                        'barcode' => 'CAT-' . strtoupper(\Illuminate\Support\Str::random(8))
                    ]
                );

                if ($category->wasRecentlyCreated) {
                    Log::info('New category created successfully', [
                        'category_id' => $category->id,
                        'category_name' => $category->name
                    ]);
                } else {
                    Log::info('Existing category found and matched', [
                        'category_id' => $category->id
                    ]);
                }

                $categoryId = $category->id;
            }
            // 2. Handle Brand
            $brandId = null;
            if (!empty($request->brand_id)) {
                Log::debug('Using existing brand_id from dropdown', ['brand_id' => $request->brand_id]);
                $brandId = $request->brand_id;
            } elseif (!empty($request->brand_name)) {
                $brandName = trim($request->brand_name);
                Log::info('Attempting to resolve brand', ['name' => $brandName]);

                $brand = \App\Models\Brand::firstOrCreate(
                    ['name' => $brandName],
                    [
                        'is_active' => true,
                    ]
                );

                if ($brand->wasRecentlyCreated) {
                    Log::info('New brand created successfully', [
                        'brand_id' => $brand->id,
                        'brand_name' => $brand->name
                    ]);
                } else {
                    Log::info('Existing brand found and matched', [
                        'brand_id' => $brand->id
                    ]);
                }

                $brandId = $brand->id;
            }

            // 3. Create Product
            $product = Product::create([
                'uuid'        => $uuid,
                'barcode'     => $validated['barcode'],
                'name'        => $validated['name'],
                'category_id' => $categoryId,
                'brand_id'    => $brandId,
                'description' => $validated['description'] ?? null,
                'note'        => $validated['note'] ?? null,
                'is_active'   => $request->boolean('is_active', true),
            ]);

            Log::info('Product created successfully', [
                'product_id'   => $product->id,
                'product_uuid' => $product->uuid,
                'category_id'  => $product->category_id,
                'brand_id'     => $product->brand_id,
                'name'         => $product->name
            ]);

            DB::commit();
            Log::debug('Database transaction committed');

            Log::info('ProductController@store - Product stored successfully', [
                'product_id' => $product->id,
                'redirect_to' => route('products.index')
            ]);

            return redirect()->route('products.index')
                ->with('success', 'Product "' . $product->name . '" created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('ProductController@store - Validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('ProductController@store - Database query error', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'code' => $e->getCode()
            ]);

            $errorMessage = 'Database error occurred. ';
            if ($e->errorInfo[1] == 1062) { // MySQL duplicate entry error
                if (str_contains($e->getMessage(), 'products.barcode')) {
                    $errorMessage = 'Barcode already exists. Please use a unique barcode.';
                } elseif (str_contains($e->getMessage(), 'products.uuid')) {
                    $errorMessage = 'UUID conflict occurred. Please try again.';
                }
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProductController@store - Unexpected error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create product. Please try again.');
        }
    }


    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();

        // Get batches with sum of on_hand quantity from related batch stocks
        $batches = $product->batches()
            ->withSum('stocks as stock_qty', 'on_hand')  // Summing 'on_hand' of all related BatchStock
            ->get();

        // Calculate total available stock for the product
        $totalStock = $batches->sum('stock_qty');  // Sum the 'stock_qty' from all batches
        $stock = $batches->pluck('stock_qty', 'id');  // [id => qty]


        return view('products.edit', compact(
            'product',
            'categories',
            'brands',
            'batches',
            'totalStock',  // Pass the total stock to the view
            'stock'
        ));
    }





    // public function update(Request $request, Product $product)
    // {
    //     Log::info('ProductController@update - Start updating product', [
    //         'product_id' => $product->id,
    //         'request_data' => $request->except('_token'),
    //         'ip' => $request->ip(),
    //         'user_agent' => $request->userAgent()
    //     ]);

    //     try {
    //         Log::debug('Validating product update data');

    //         // -------------------------
    //         // Validation Rules
    //         // -------------------------
    //         $validationRules = [
    //             'barcode' => 'required|string|max:50|unique:products,barcode,' . $product->id,
    //             'name' => 'required|string|max:255',
    //             'description' => 'nullable|string',
    //             'note' => 'nullable|string',
    //             'is_active' => 'boolean',
    //         ];

    //         // Only validate category_id if provided
    //         if ($request->filled('category_id')) {
    //             $validationRules['category_id'] = 'exists:categories,id';
    //         }

    //         // Only validate brand_id if provided
    //         if ($request->filled('brand_id')) {
    //             $validationRules['brand_id'] = 'exists:brands,id';
    //         }

    //         // Validate category_name only if category_id is NOT provided
    //         if ($request->filled('category_name') && !$request->filled('category_id')) {
    //             $validationRules['category_name'] = 'string|max:255';
    //         }

    //         // Validate brand_name only if brand_id is NOT provided
    //         if ($request->filled('brand_name') && !$request->filled('brand_id')) {
    //             $validationRules['brand_name'] = 'string|max:255';
    //         }

    //         $validated = $request->validate($validationRules);
    //         Log::debug('Validation passed', ['validated_data' => $validated]);

    //         DB::beginTransaction();
    //         Log::debug('Database transaction started');

    //         // -------------------------
    //         // Resolve Category
    //         // -------------------------
    //         $categoryId = null;

    //         if (!empty($request->category_id)) {
    //             Log::debug('Using existing category_id', [
    //                 'category_id' => $request->category_id
    //             ]);
    //             $categoryId = $request->category_id;
    //         } elseif (!empty($request->category_name)) {
    //             $categoryName = trim($request->category_name);

    //             Log::info('Resolving category by name', [
    //                 'category_name' => $categoryName
    //             ]);

    //             $category = Category::firstOrCreate(
    //                 ['name' => $categoryName],
    //                 [
    //                     'uuid' => (string) \Illuminate\Support\Str::uuid(),
    //                     'is_active' => true,
    //                     'barcode' => 'CAT-' . strtoupper(\Illuminate\Support\Str::random(8)),
    //                 ]
    //             );

    //             Log::info(
    //                 $category->wasRecentlyCreated
    //                     ? 'New category created'
    //                     : 'Existing category matched',
    //                 ['category_id' => $category->id]
    //             );

    //             $categoryId = $category->id;
    //         }

    //         // -------------------------
    //         // Resolve Brand
    //         // -------------------------
    //         $brandId = null;

    //         if (!empty($request->brand_id)) {
    //             Log::debug('Using existing brand_id', [
    //                 'brand_id' => $request->brand_id
    //             ]);
    //             $brandId = $request->brand_id;
    //         } elseif (!empty($request->brand_name)) {
    //             $brandName = trim($request->brand_name);

    //             Log::info('Resolving brand by name', [
    //                 'brand_name' => $brandName
    //             ]);

    //             $brand = \App\Models\Brand::firstOrCreate(
    //                 ['name' => $brandName],
    //                 ['is_active' => true]
    //             );

    //             Log::info(
    //                 $brand->wasRecentlyCreated
    //                     ? 'New brand created'
    //                     : 'Existing brand matched',
    //                 ['brand_id' => $brand->id]
    //             );

    //             $brandId = $brand->id;
    //         }

    //         // -------------------------
    //         // Update Product
    //         // -------------------------
    //         $product->update([
    //             'barcode'     => $validated['barcode'],
    //             'name'        => $validated['name'],
    //             'category_id' => $categoryId,
    //             'brand_id'    => $brandId,
    //             'description' => $validated['description'] ?? null,
    //             'note'        => $validated['note'] ?? null,
    //             'is_active'   => $request->boolean('is_active', $product->is_active),
    //         ]);

    //         Log::info('Product updated successfully', [
    //             'product_id' => $product->id,
    //             'category_id' => $categoryId,
    //             'brand_id' => $brandId
    //         ]);

    //         DB::commit();
    //         Log::debug('Database transaction committed');

    //         return redirect()->route('products.index')
    //             ->with('success', 'Product "' . $product->name . '" updated successfully.');
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         Log::warning('ProductController@update - Validation failed', [
    //             'errors' => $e->errors(),
    //             'input' => $request->all()
    //         ]);

    //         return redirect()->back()
    //             ->withErrors($e->validator)
    //             ->withInput();
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         DB::rollBack();

    //         Log::error('ProductController@update - Database error', [
    //             'error' => $e->getMessage(),
    //             'sql' => $e->getSql(),
    //             'bindings' => $e->getBindings(),
    //             'code' => $e->getCode()
    //         ]);

    //         $errorMessage = 'Database error occurred.';
    //         if ($e->errorInfo[1] == 1062 && str_contains($e->getMessage(), 'products.barcode')) {
    //             $errorMessage = 'Barcode already exists. Please use a unique barcode.';
    //         }

    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', $errorMessage);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         Log::error('ProductController@update - Unexpected error', [
    //             'error' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', 'Failed to update product. Please try again.');
    //     }
    // }



    public function update(Request $request, Product $product)
    {
        Log::info('ProductController@update - Start updating product', [
            'product_id' => $product->id,
            'request_data' => $request->except('_token'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            Log::debug('Validating product update data');

            // -------------------------
            // Validation Rules
            // -------------------------
            $validationRules = [
                'barcode' => 'required|string|max:50|unique:products,barcode,' . $product->id,
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'note' => 'nullable|string',
                'is_active' => 'boolean',
                'images' => 'nullable|array|max:10',  // Allow up to 10 images
                'images.*' => 'nullable|image|max:5120',  // Each image must be an image file with size limit
                'image_url' => 'nullable|array',  // Allow an array of image URLs (base64 or external)
                'image_url.*' => 'nullable|string', // Allow string for base64 or URLs
            ];

            $validated = $request->validate($validationRules);
            Log::debug('Validation passed', ['validated_data' => $validated]);

            DB::beginTransaction();
            Log::debug('Database transaction started');

            // -------------------------
            // Resolve Category
            // -------------------------
            $categoryId = $this->resolveCategory($request);

            // -------------------------
            // Resolve Brand
            // -------------------------
            $brandId = $this->resolveBrand($request);

            // -------------------------
            // Update Product
            // -------------------------
            $product->update([
                'barcode'     => $validated['barcode'],
                'name'        => $validated['name'],
                'category_id' => $categoryId,
                'brand_id'    => $brandId,
                'description' => $validated['description'] ?? null,
                'note'        => $validated['note'] ?? null,
                'is_active'   => $request->boolean('is_active', $product->is_active),
            ]);

            Log::info('Product updated successfully', [
                'product_id' => $product->id,
                'category_id' => $categoryId,
                'brand_id' => $brandId
            ]);

            // -------------------------
            // Handle Image Uploads
            // -------------------------
            $this->handleImageUploads($request, $product);

            DB::commit();
            Log::debug('Database transaction committed');

            return redirect()->route('products.index')
                ->with('success', 'Product "' . $product->name . '" updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('ProductController@update - Validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            Log::error('ProductController@update - Database error', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'code' => $e->getCode()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Database error occurred.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('ProductController@update - Unexpected error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }
    }

    // Helper function to handle image uploads and base64 images
    protected function handleImageUploads(Request $request, Product $product)
    {
        // Handle regular image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => ($index === 0), // Set first image as primary
                ]);
            }
        }

        // Handle base64 images
        if ($request->has('image_url')) {
            foreach ($request->input('image_url') as $index => $url) {
                if ($this->isBase64($url)) {
                    // If it's a base64 image, decode and save
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $url));
                    $fileName = uniqid() . '.jpg'; // Generate a unique name for the file
                    $path = 'products/' . $fileName;
                    Storage::disk('public')->put($path, $imageData);

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => ($index === 0), // Set first image as primary
                    ]);
                } else {
                    // Handle external image URL (if needed)
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $url, // Store external URL directly
                        'is_primary' => ($index === 0), // Set first image as primary
                    ]);
                }
            }
        }
    }

    // Helper function to check if string is a base64 encoded image
    protected function isBase64($str)
    {
        return preg_match('/^data:image\/(jpeg|png|gif|webp);base64,/', $str);
    }

    // Helper function to resolve category ID
    protected function resolveCategory(Request $request)
    {
        $categoryId = null;
        if ($request->filled('category_id')) {
            $categoryId = $request->category_id;
        } elseif ($request->filled('category_name')) {
            $categoryName = trim($request->category_name);
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                [
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'is_active' => true,
                    'barcode' => 'CAT-' . strtoupper(\Illuminate\Support\Str::random(8)),
                ]
            );
            $categoryId = $category->id;
        }
        return $categoryId;
    }

    // Helper function to resolve brand ID
    protected function resolveBrand(Request $request)
    {
        $brandId = null;
        if ($request->filled('brand_id')) {
            $brandId = $request->brand_id;
        } elseif ($request->filled('brand_name')) {
            $brandName = trim($request->brand_name);
            $brand = \App\Models\Brand::firstOrCreate(
                ['name' => $brandName],
                ['is_active' => true]
            );
            $brandId = $brand->id;
        }
        return $brandId;
    }






    /**
     * Remove the specified product from storage.
     */

    public function destroy(Request $request, Product $product)
    {
        Log::info('DESTROY HIT', [
            'method' => request()->method(),
            'path' => request()->path(),
        ]);
        // Gather dependency counts for tracking (optional but helpful)
        $batchIds = $product->batches()->pluck('id');

        $counts = [
            'product_id'   => $product->id,
            'batches'      => $batchIds->count(),
            'return_items' => \App\Models\ReturnItem::whereIn('product_batch_id', $batchIds)->count(),
            'order_items'  => \App\Models\OrderItem::whereIn('product_batch_id', $batchIds)->count(),
            'batch_stocks' => \App\Models\BatchStock::whereIn('product_batch_id', $batchIds)->count(),
            'stock_ledgers' => \App\Models\StockLedger::whereIn('product_batch_id', $batchIds)->count(),
            'tx_lines'     => \App\Models\StockTransactionLine::whereIn('product_batch_id', $batchIds)->count(),
            'cart_items'   => \App\Models\CartItem::where('product_id', $product->id)->count(),
            'images'       => $product->images()->count(),
            'statuses'     => $product->statuses()->count(),
        ];

        Log::warning('Product archive attempt', $counts);

        try {
            // Track who + why
            $product->deleted_by = Auth::id();
            $product->deleted_reason = $request->input('deleted_reason'); // optional field from form
            $product->save();

            // Soft delete (won’t break FK history)
            $product->delete();

            Log::info('Product archived (soft deleted)', ['product_id' => $product->id]);

            return back()->with('success', 'Product archived successfully (can be restored).');
        } catch (QueryException $e) {
            Log::error('Product archive failed', [
                'product_id' => $product->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Archive failed. Check logs.');
        }
    }


    // In your ProductController.php
    /**
     * Live search products by name or barcode.
     */
    public function liveSearch(Request $request)
    {
        $query = $request->get('q');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::with(['category', 'brand', 'images', 'batches'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('barcode', 'like', '%' . $query . '%')
                    ->orWhere('description', 'like', '%' . $query . '%');
            })
            ->limit(50) // Increased limit for table display
            ->get();

        return response()->json($products);
    }





    public function show(Product $product)
    {
        $product->load([
            'images' => fn($q) => $q->orderByDesc('is_primary'),
            'category',
            'brand',
            'batches' => function ($q) {
                $q->orderBy('expiry_date', 'asc')
                    ->with([
                        'stocks' => function ($sq) {
                            $sq->where('location_id', '!=', null)
                                ->with('location:id,name,code,is_active')
                                ->orderBy('location_id');
                        }
                    ]);
            },
        ]);

        // for filter dropdown
        $locations = Location::query()
            ->select(['id', 'name', 'code', 'is_active'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('products.show', compact('product', 'locations'));
    }


    public function checkBarcode(Request $request)
    {
        $barcode = trim((string)$request->get('barcode', ''));

        if ($barcode === '' || strlen($barcode) < 3) {
            return response()->json(['valid' => false, 'message' => 'Barcode too short']);
        }

        $exists = \App\Models\Product::where('barcode', $barcode)->exists();

        return response()->json([
            'valid' => !$exists,
            'message' => $exists ? 'Barcode already exists' : 'Barcode is available'
        ]);
    }


    // Barcode Print
    public function barcodeprint(ProductBatch $batch)
    {
        $batch->load('product');
        return view('products.print.batch_barcode', compact('batch'));
    }

    public function trash()
    {
        $products = \App\Models\Product::onlyTrashed()
            ->latest('deleted_at')
            ->paginate(20);

        return view('products.trash', compact('products'));
    }
    public function restore($id)
    {
        $product = \App\Models\Product::onlyTrashed()->findOrFail($id);

        $product->restore();

        return redirect()->route('products.trash')->with('success', 'Product restored successfully.');
    }
    public function forceDelete($id)
    {
        $product = \App\Models\Product::onlyTrashed()->findOrFail($id);

        // ⚠️ This will fail if exchange_lines/order_items/etc reference product_id
        $product->forceDelete();

        return redirect()->route('products.trash')->with('success', 'Product permanently deleted.');
    }

    /**
 * Bulk restore deleted products
 */
public function bulkRestore(Request $request)
{
    try {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
        ]);

        $restoredCount = Product::onlyTrashed()
            ->whereIn('id', $request->ids)
            ->restore();

        return response()->json([
            'success' => true,
            'message' => "Successfully restored {$restoredCount} product(s)."
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to restore products.'
        ], 500);
    }
}

/**
 * Bulk force delete products
 */
public function bulkForceDelete(Request $request)
{
    try {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
        ]);

        $deletedCount = Product::onlyTrashed()
            ->whereIn('id', $request->ids)
            ->forceDelete();

        return response()->json([
            'success' => true,
            'message' => "Successfully permanently deleted {$deletedCount} product(s)."
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete products.'
        ], 500);
    }
}
}


