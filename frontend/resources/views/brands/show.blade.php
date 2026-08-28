<x-layout :title="$brand->name" :description="'Shop ' . $brand->name . ' products at JOJOBI MART.'">
    <div class="container-page py-10">
        <nav class="text-xs text-ink-soft font-mono mb-4">
            <a href="{{ route('home') }}" class="hover:text-ink">Home</a> /
            <a href="{{ route('brands.index') }}" class="hover:text-ink">Brands</a> /
            <span class="text-ink">{{ $brand->name }}</span>
        </nav>

        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="eyebrow">Brand</p>
                <h1 class="font-display text-3xl sm:text-4xl mt-1">{{ $brand->name }}</h1>
                @if ($brand->description)
                    <p class="text-ink-soft text-sm mt-2 max-w-xl">{{ $brand->description }}</p>
                @endif
            </div>
            <p class="text-sm text-ink-soft">{{ $products->total() }} products</p>
        </div>

        @include('partials.product-grid')
    </div>
</x-layout>
