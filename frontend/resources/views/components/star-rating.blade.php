@props(['rating' => 0, 'size' => 'w-4 h-4'])

<div class="flex items-center gap-0.5" aria-label="{{ number_format($rating, 1) }} out of 5 stars">
    @for ($i = 1; $i <= 5; $i++)
        <svg class="{{ $size }} {{ $i <= round($rating) ? 'text-accent' : 'text-line' }}" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2.5l2.9 6.1 6.6.8-4.9 4.5 1.3 6.6L12 17.6l-5.9 3 1.3-6.6-4.9-4.5 6.6-.8L12 2.5z"/>
        </svg>
    @endfor
</div>
