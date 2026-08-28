<x-layout title="Categories" description="Browse every category at JOJOBI MART.">
    <div class="container-page py-10">
        <p class="eyebrow">Browse</p>
        <h1 class="font-display text-3xl sm:text-4xl mt-1 mb-8">All categories</h1>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($categories as $cat)
                <a href="{{ route('categories.show', $cat) }}" class="card hover-lift rounded-xl p-6 flex items-center justify-between">
                    <div>
                        <p class="font-display text-lg">{{ $cat->name }}</p>
                        <p class="text-xs text-ink-soft font-mono mt-1">{{ $cat->products_count }} products</p>
                    </div>
                    <svg class="w-4 h-4 text-ink-soft" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6"/></svg>
                </a>
            @endforeach
        </div>
    </div>
</x-layout>
