@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>🗑️ Deleted Product Batches</h2>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($batches->count() === 0)
        <div class="alert alert-info">No deleted batches found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Batch No</th>
                        <th>SKU</th>
                        <th>Expiry</th>
                        <th>Deleted At</th>
                        <th style="width:220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batches as $b)
                        <tr>
                            <td>{{ $b->id }}</td>
                            <td>{{ $b->product?->name ?? 'Product not found' }}</td>
                            <td>{{ $b->batch_no ?? $b->id }}</td>
                            <td>{{ $b->batch_sku ?? '—' }}</td>
                            <td>{{ $b->expiry_date?->format('Y-m-d') ?? '—' }}</td>
                            <td>{{ $b->deleted_at }}</td>
                            <td class="d-flex gap-2">
                                {{-- Restore --}}
                                <form method="POST" action="{{ route('product-batches.restore', $b->id) }}">
                                    @csrf
                                    <button class="btn btn-success btn-sm"
                                        onclick="return confirm('Restore this batch?')">
                                        Restore
                                    </button>
                                </form>

                                {{-- Optional Force Delete --}}
                                {{-- You can remove this button if you don’t want permanent delete --}}
                                <form method="POST" action="{{ route('product-batches.forceDelete', $b->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Permanently delete? This cannot be undone!')">
                                        Force Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $batches->links() }}
    @endif
</div>
@endsection
