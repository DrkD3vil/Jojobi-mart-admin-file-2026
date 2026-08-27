<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->query('q', ''));
        $type = trim((string)$request->query('type', ''));
        $status = trim((string)$request->query('status', ''));

        $types = $this->types();

        $locations = $this->baseQuery($q, $type, $status)
            ->paginate(15)
            ->withQueryString();

        return view('locations.index', compact('locations', 'q', 'type', 'status', 'types'));
    }

    // ✅ THIS FIXES YOUR ERROR
    public function show(Location $location)
    {
        // If you don't need show page, redirect to edit.
        return redirect()->route('locations.edit', $location);
    }

    public function ajaxIndex(Request $request)
    {
        $q = trim((string)$request->query('q', ''));
        $type = trim((string)$request->query('type', ''));
        $status = trim((string)$request->query('status', ''));
        $perPage = (int)$request->query('per_page', 15);
        $perPage = max(5, min(100, $perPage));

        $locations = $this->baseQuery($q, $type, $status)
            ->paginate($perPage)
            ->withQueryString();

        $rows = $locations->getCollection()->map(function (Location $l) {
            return [
                'id' => $l->id,
                'name' => $l->name,
                'code' => $l->code,
                'type' => $l->type,
                'is_active' => (bool)$l->is_active,
                'edit_url' => route('locations.edit', $l),
                'delete_url' => route('locations.ajaxDestroy', $l),
                'toggle_url' => route('locations.toggle', $l),
            ];
        })->values();

        return response()->json([
            'rows' => $rows,
            'meta' => [
                'count_on_page' => $rows->count(),
                'total' => $locations->total(),
                'current_page' => $locations->currentPage(),
                'last_page' => $locations->lastPage(),
                'per_page' => $locations->perPage(),
            ],
            'pagination_html' => $locations->links()->render(),
        ]);
    }

    public function create()
    {
        $types = $this->types();
        return view('locations.create', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'code' => ['nullable','string','max:50','unique:locations,code'],
            'type' => ['required', Rule::in(array_keys($this->types()))],
            'is_active' => ['nullable','boolean'],
            'address' => ['nullable','string'],
            'notes' => ['nullable','string'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? false);

        Location::create($data);

        return redirect()->route('locations.index')->with('success', 'Location created successfully.');
    }

    public function edit(Location $location)
    {
        $types = $this->types();
        return view('locations.edit', compact('location', 'types'));
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'code' => ['nullable','string','max:50', Rule::unique('locations','code')->ignore($location->id)],
            'type' => ['required', Rule::in(array_keys($this->types()))],
            'is_active' => ['nullable','boolean'],
            'address' => ['nullable','string'],
            'notes' => ['nullable','string'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? false);

        $location->update($data);

        return redirect()->route('locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'Location deleted successfully.');
    }

    public function ajaxDestroy(Location $location)
    {
        $location->delete();
        return response()->json(['ok' => true]);
    }

    public function toggleActive(Location $location)
    {
        $location->is_active = !$location->is_active;
        $location->save();

        return response()->json([
            'ok' => true,
            'id' => $location->id,
            'is_active' => (bool)$location->is_active,
        ]);
    }

    private function baseQuery(string $q, string $type, string $status)
    {
        return Location::query()
            ->select(['id','name','code','type','is_active','address','notes','created_at'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('code', 'like', "%{$q}%");
                    if (ctype_digit($q)) $w->orWhere('id', (int)$q);
                });
            })
            ->when($type !== '' && array_key_exists($type, $this->types()), fn($qq) => $qq->where('type', $type))
            ->when($status === 'active', fn($qq) => $qq->where('is_active', true))
            ->when($status === 'inactive', fn($qq) => $qq->where('is_active', false))
            ->orderByDesc('id');
    }

    private function types(): array
    {
        return [
            'warehouse' => 'Warehouse',
            'store' => 'Store',
            'pos' => 'POS',
            'damaged' => 'Damaged',
            'return_holding' => 'Return Holding',
        ];
    }
}
