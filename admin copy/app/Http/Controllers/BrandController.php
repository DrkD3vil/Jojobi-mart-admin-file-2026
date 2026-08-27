<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::latest()->paginate(10);
        return view('brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image',
        ]);

        $data = $request->only(['name', 'description', 'is_active']);
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        Brand::create($data);

        return redirect()->route('brands.index')->with('success', 'Brand created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'description']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($data);

        return redirect()
            ->route('brands.index')
            ->with('success', 'Brand updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();
        return back()->with('success', 'Brand deleted');
    }

    /**
     * Toggle the active status of the brand.
     */

    public function toggleStatus(Request $request, Brand $brand)
    {
        Log::info('Toggle Request', $request->all());

        $request->validate([
            'is_active' => 'required',
        ]);

        // FORCE INT (THIS FIXES 99% ISSUES)
        $brand->is_active = $request->is_active ? 1 : 0;
        $brand->save();

        return response()->json([
            'success' => true,
            'brand_id' => $brand->id,
            'is_active' => $brand->is_active,
        ]);
    }


    /**
     * Live search brands.
     */




    // In BrandController.php - Add this method for testing

    // public function search(Request $request)
    // {
    //     try {
    //         $q = $request->get('q');

    //         if (!$q) {
    //             return response()->json([]);
    //         }

    //         $brands = Brand::query()
    //             ->where('name', 'like', "%{$q}%")
    //             ->select('id', 'name')
    //             ->limit(10)
    //             ->get();

    //         return response()->json($brands);

    //     } catch (\Exception $e) {

    //         Log::error('Brand search failed', [
    //             'error' => $e->getMessage()
    //         ]);

    //         return response()->json([], 500);
    //     }
    // }



// BrandController.php
public function search(Request $request)
{
    try {
        Log::info('Brand search input', $request->all());
        $query = trim($request->input('q', ''));

        if (strlen($query) < 2) {
            return response()->json(['status' => true, 'data' => []]);
        }

        $brands = Brand::where('is_active', true)
            ->where('name', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name']);

        Log::info('Brand search results', ['count' => $brands->count()]);
        return response()->json(['status' => true, 'data' => $brands]);

    } catch (\Throwable $e) {
        Log::error('Brand search failed', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);
        return response()->json(['status' => false, 'message' => 'Server error'], 500);
    }
}

}
