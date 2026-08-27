@extends('layouts.app')

@section('content')
<div class="container animate-fade-in">

    <div class="page-header mb-4">
        <div class="header-content">
            <h1 class="page-title">Image Trash</h1>
            <p class="page-subtitle">Restore or permanently delete images</p>

            <nav class="breadcrumb" aria-label="Breadcrumb">
                <div class="breadcrumb-item">
                    <a href="" class="breadcrumb-link">Dashboard</a>
                </div>
                <div class="breadcrumb-item">
                    <a href="{{ route('product.images.all') }}" class="breadcrumb-link">All Images</a>
                </div>
                <div class="breadcrumb-item">
                    <span class="breadcrumb-current">Trash</span>
                </div>
            </nav>

            <div class="page-status">
                <span class="status-badge danger">
                    Trash: {{ $trashedImageCount ?? 0 }}
                </span>
            </div>
        </div>

        <div class="header-actions">
            <a href="{{ route('product.images.all') }}" class="header-btn secondary">← Back</a>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('info'))    <div class="alert alert-info">{{ session('info') }}</div> @endif
    @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="cardx animate-slide-up">
        <div class="cardx-hd">
            <div>
                <h2 class="title">Trashed Images</h2>
                <p class="subtle">Force delete removes file from storage</p>
            </div>
        </div>

        <div class="p-4">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th width="90">Preview</th>
                            <th>Product</th>
                            <th width="120">Deleted At</th>
                            <th width="260">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($images as $img)
                            <tr>
                                <td>
                                    <img src="{{ asset('storage/'.$img->image_path) }}" width="80" style="border-radius:10px;">
                                </td>
                                <td>
                                    {{ $img->product?->name ?? 'Product deleted' }}
                                </td>
                                <td class="small text-muted">
                                    {{ $img->deleted_at }}
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <form method="POST" action="{{ route('product-images.restore', $img->id) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-success"
                                                    onclick="return confirm('Restore this image?')">
                                                Restore
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('product-images.forceDelete', $img->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Permanently delete? This removes the file too!')">
                                                Force Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">Trash is empty.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $images->links() }}
        </div>
    </div>
</div>
@endsection
