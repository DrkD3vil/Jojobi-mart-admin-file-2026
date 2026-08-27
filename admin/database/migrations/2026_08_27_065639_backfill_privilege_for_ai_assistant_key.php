<?php

use HasinHayder\Tyro\Models\Privilege;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    private const KEY = 'ai_assistant';
    private const LABEL = 'AI Assistant';

    /**
     * Every access_key needs a backing Privilege row before a
     * privilege_access_keys mapping can reference it (privilege_id is a
     * required, non-nullable FK) -- mirrors the one-time backfill that did
     * this for the original ~20 access keys.
     */
    public function up(): void
    {
        if (Privilege::where('access_key', self::KEY)->exists()) {
            return;
        }

        $slug = self::KEY;
        $suffix = 1;
        while (Privilege::where('slug', $slug)->exists()) {
            $slug = self::KEY . '-' . (++$suffix);
        }

        $privilege = Privilege::create([
            'name' => self::LABEL,
            'slug' => $slug,
            'description' => 'Grants the "' . self::LABEL . '" access key when assigned to a role.',
        ]);

        // access_key isn't in the vendor model's $fillable, so it can't be
        // mass-assigned via create() -- set it directly instead.
        $privilege->access_key = self::KEY;
        $privilege->save();
    }

    public function down(): void
    {
        Privilege::where('access_key', self::KEY)->delete();
    }
};
