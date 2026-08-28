<?php

namespace App\Models\Concerns;

use App\Models\Timeline;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTimeline
{
    public function timeline(): MorphMany
    {
        return $this->morphMany(Timeline::class, 'timelineable')->latest('id');
    }

    public function recordTimeline(
        string $event,
        string $title,
        ?string $description = null,
        ?string $from = null,
        ?string $to = null,
        ?string $icon = null,
        array $meta = []
    ): Timeline {
        return $this->timeline()->create([
            'event' => $event,
            'title' => $title,
            'description' => $description,
            'from_value' => $from,
            'to_value' => $to,
            'icon' => $icon ?? $event,
            'caused_by' => null,
            'meta' => $meta,
        ]);
    }
}
