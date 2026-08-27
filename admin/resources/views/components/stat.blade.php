@props(['title','value','money'=>false,'positive'=>false,'negative'=>false])

<div class="fade-in-item rounded-[var(--radius)] border p-4 transition-all duration-200 hover:-translate-y-0.5"
     style="background-color: var(--card); border-color: var(--border); box-shadow: var(--card-shadow); color: var(--card-foreground);">
    <p class="text-xs uppercase" style="font-family: var(--font-mono); letter-spacing: 0.06em; color: var(--muted-foreground);">{{ $title }}</p>
    <h2 class="mt-1" style="font-family: var(--font-display); font-weight: 500; font-size: clamp(1.4rem, 2.1vw, 1.9rem); line-height: 1;
        color: {{ $positive ? 'var(--success)' : ($negative ? 'var(--danger)' : 'var(--primary)') }};">
        {{ $money ? number_format($value, 2) : $value }}
    </h2>
</div>
