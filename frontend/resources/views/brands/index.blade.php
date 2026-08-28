<x-layout title="Brands" description="Every brand stocked at JOJOBI MART.">
    <div class="container-page py-10">
        <p class="eyebrow">Browse</p>
        <h1 class="font-display text-3xl sm:text-4xl mt-1 mb-8">All brands</h1>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($brands as $brand)
                <a href="{{ route('brands.show', $brand) }}" class="card hover-lift rounded-xl p-6 flex items-center justify-between">
                    <div>
                        <p class="font-display text-lg">{{ $brand->name }}</p>
                        <p class="text-xs text-ink-soft font-mono mt-1">{{ $brand->products_count }} products</p>
                    </div>
                    <svg class="w-4 h-4 text-ink-soft" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg>
                </a>
            @endforeach
        </div>
    </div>
</x-layout>
