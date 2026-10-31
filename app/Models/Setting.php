<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    private const CACHE_KEY = 'settings.all';

    public static function cached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::cached()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    /** Settings stored as JSON (lists of photos, etc.). */
    public static function getJson(string $key): array
    {
        return json_decode((string) static::get($key, '[]'), true) ?: [];
    }

    public static function put(string|array $key, mixed $value = null): void
    {
        $values = is_array($key) ? $key : [$key => $value];

        foreach ($values as $k => $v) {
            if (is_array($v)) {
                $v = json_encode(array_values($v));
            }
            static::query()->updateOrCreate(['key' => $k], ['value' => $v === null ? null : (string) $v]);
        }

        Cache::forget(self::CACHE_KEY);
    }
}
