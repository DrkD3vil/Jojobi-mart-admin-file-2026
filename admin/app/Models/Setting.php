<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Simple key/value store for site-wide settings (store identity, currency
 * symbol, etc.). Values are cached for the lifetime of the request and
 * beyond via Cache::rememberForever(), invalidated on every save, so
 * reading a setting (e.g. from a helper called on every page) never costs
 * more than one cache read.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    private const CACHE_KEY = 'app_settings';

    private static ?array $memo = null;

    public static function cached(): array
    {
        return self::$memo ??= Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()->pluck('value', 'key')->all();
        });
    }

    public static function get(string $key, $default = null)
    {
        return static::cached()[$key] ?? $default;
    }

    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        self::$memo = null;
        Cache::forget(self::CACHE_KEY);
    }
}
