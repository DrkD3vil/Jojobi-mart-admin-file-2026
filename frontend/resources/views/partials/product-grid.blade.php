@if ($products->isEmpty())
    <div class="text-center py-24">
        <svg class="w-12 h-12 mx-auto text-ink-soft mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M20 20l-4.8-4.8"/></svg>
        <p class="text-ink-soft">No products found. Try a different filter.</p>
    </div>
@else
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-x-5 gap-y-10">
        @foreach ($products as $product)
            <x-product-card :product="$product" :wished="in_array($product->id, $wishedIds ?? [])" />
        @endforeach
    </div>

    <div class="mt-12">
        {{ $products->onEachSide(1)->links() }}
    </div>
@endif
