{{--
    Shared order-history timeline, included by both account/order-show.blade.php
    and track/show.blade.php. Reads $order's `timeline` relation (see
    App\Models\Concerns\HasTimeline, shared with the admin app's Order model
    since both apps read/write the same `timelines` table). Adapts the
    dot/line vertical-timeline pattern from admin's public/order.blade.php to
    this app's Tailwind utility classes -- same visual shape (dot, connecting
    line, title, description, timestamp), not the literal admin styling.
--}}
{{-- Expects a $timeline collection of Timeline models, passed in via @include('partials.order-timeline', ['timeline' => ...]). --}}
@php
    $events = $timeline->sortBy('created_at')->values();
@endphp

@if ($events->isNotEmpty())
    <div class="card rounded-2xl p-6">
        <p class="label mb-4">Order timeline</p>
        <ul>
            @foreach ($events as $event)
                <li class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <span class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0 {{ $loop->last ? 'bg-accent' : 'bg-teal' }}"></span>
                        @if (!$loop->last)
                            <span class="w-px flex-1 bg-line my-1"></span>
                        @endif
                    </div>
                    <div class="flex-1 {{ $loop->last ? 'pb-0' : 'pb-5' }}">
                        <p class="text-sm font-medium">{{ $event->title }}</p>
                        @if ($event->description)
                            <p class="text-xs text-ink-soft mt-1">{{ $event->description }}</p>
                        @endif
                        <p class="text-xs text-ink-soft/70 font-mono mt-1">{{ $event->created_at->format('d M Y, g:ia') }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif
