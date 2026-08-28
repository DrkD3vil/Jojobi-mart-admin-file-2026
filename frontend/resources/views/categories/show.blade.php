<x-layout :title="$category->name" :description="'Shop ' . $category->name . ' at JOJOBI MART.'">
    <div class="container-page py-10">
        <nav class="text-xs text-ink-soft font-mono mb-4">
            <a href="{{ route('home') }}" class="hover:text-ink">Home</a> /
            <a href="{{ route('categories.index') }}" class="hover:text-ink">Categories</a> /
            <span class="text-ink">{{ $category->name }}</span>
        </nav>

        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="eyebrow">Category</p>
                <h1 class="font-display text-3xl sm:text-4xl mt-1">{{ $category->name }}</h1>
            </div>
            <p class="text-sm text-ink-soft">{{ $products->total() }} products</p>
        </div>

        @include('partials.product-grid')
    </div>
</x-layout>
