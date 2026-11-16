<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'label',
        'type',
    ];

    public static function allKeyed(): array
    {
        return Cache::remember('upg_site_settings', 300, function () {
            return self::query()->pluck('value', 'key')->toArray();
        });
    }

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $settings = self::allKeyed();

        return $settings[$key] ?? $default;
    }

    public static function clearCache(): void
    {
        Cache::forget('upg_site_settings');
    }
}

