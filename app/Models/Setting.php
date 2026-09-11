<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        try {
            return Cache::rememberForever('setting.'.$key, function () use ($key, $default) {
                $setting = static::query()->where('key', $key)->first();

                return $setting?->value ?? $default;
            });
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('setting.'.$key);
    }
}
