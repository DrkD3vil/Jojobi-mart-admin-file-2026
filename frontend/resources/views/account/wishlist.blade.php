<x-layout title="Wishlist" description="Products you've saved at JOJOBI MART.">
    <div class="container-page py-10">
        <p class="eyebrow">My account</p>
        <h1 class="font-display text-3xl sm:text-4xl mt-1 mb-8">Wishlist</h1>

        <div class="grid lg:grid-cols-[240px_1fr] gap-8">
            @include('partials.account-nav')

            <div>
                @if ($items->isEmpty())
                    <div class="card rounded-2xl text-center py-20">
                        <svg class="w-12 h-12 mx-auto text-ink-soft mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 21s-7-4.6-9.5-9C.8 8.4 2.4 4.5 6 4c2-.3 3.7.8 6 3 2.3-2.2 4-3.3 6-3 3.6.5 5.2 4.4 3.5 8-2.5 4.4-9.5 9-9.5 9z"/></svg>
                        <p class="text-ink-soft mb-6">Nothing saved yet. Tap the heart on any product to add it here.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-stamp">Browse products</a>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-5 gap-y-10">
                        @foreach ($items as $wish)
                            @if ($wish->product)
                                <x-product-card :product="$wish->product" :wished="true" />
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
