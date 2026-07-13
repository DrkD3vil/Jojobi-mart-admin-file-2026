@extends('layouts.app')

@section('content')
<div class="container animate-fade-in">

    <div class="page-header mb-4">
        <div class="header-content">
            <h1 class="page-title">All Product Images</h1>
            <p class="page-subtitle">Manage images across all products</p>

            <nav class="breadcrumb" aria-label="Breadcrumb">
                <div class="breadcrumb-item">
                    <a href="" class="breadcrumb-link">Dashboard</a>
                </div>
                <div class="breadcrumb-item">
                    <a href="{{ route('products.index') }}" class="breadcrumb-link">Products</a>
                </div>
                <div class="breadcrumb-item">
                    <span class="breadcrumb-current">All Images</span>
                </div>
            </nav>

            <div class="page-status">
                <span class="status-badge info">
                    Total: {{ $images->total() }}
                </span>
                <span class="status-badge danger">
                    Trash: {{ $trashedImageCount ?? 0 }}
                </span>
                <span class="status-badge warning">
                    Orphan: {{ $orphanCount ?? 0 }}
                </span>
            </div>
        </div>

        <div class="header-actions">
            <a href="{{ route('product-images.trash') }}" class="header-btn secondary">
                🗑️ Trash
            </a>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('info'))    <div class="alert alert-info">{{ session('info') }}</div> @endif
    @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="cardx animate-slide-up">
        <div class="cardx-hd">
            <div>
                <h2 class="title">Images</h2>
                <p class="subtle">Trash works even if product is missing</p>
            </div>
        </div>

        <div class="p-4">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="90">Preview</th>
                            <th>Product</th>
                            <th width="90">Primary</th>
                            <th width="320">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($images as $img)
                            @php $p = $img->product; @endphp
                            <tr>
                                <td>
                                    <img src="{{ asset('storage/'.$img->image_path) }}" width="80" style="border-radius:10px;">
                                </td>

                                <td>
                                    @if($p)
                                        <div class="fw-semibold">{{ $p->name }}</div>
                                        <div class="small text-muted">#{{ $p->id }}</div>
                                    @else
                                        <span class="badge bg-warning text-dark">Orphan (product deleted)</span>
                                    @endif
                                </td>

                                <td>
                                    @if($img->is_primary)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        No
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-2">

                                        {{-- Manage product images --}}
                                        @if($p)
                                            <a class="btn btn-sm btn-outline-primary"
                                               href="{{ route('products.images.index', $p->id) }}">
                                               🖼️ Manage
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" disabled>🖼️ Manage</button>
                                        @endif

                                        {{-- Set primary needs product --}}
                                        @if($p)
                                            <form method="POST" action="{{ route('products.images.primary', [$p->id, $img->id]) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-success"
                                                        onclick="return confirm('Set as primary?')">
                                                    ⭐ Primary
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" disabled>⭐ Primary</button>
                                        @endif

                                        {{-- Trash globally (works even if product missing) --}}
                                        <form method="POST" action="{{ route('product-images.deleteById', $img->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Move to trash?')">
                                                🗑️ Trash
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">No images found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $images->links() }}
        </div>
    </div>
</div>
@endsection
