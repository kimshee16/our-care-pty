<?php

namespace App\Support;

use App\Models\CmsSetting;
class CmsContent
{
    public static function all(): array
    {
        $content = config('cms');

        foreach (CmsSetting::all() as $setting) {
            data_set($content, $setting->key, json_decode((string) $setting->value, true));
        }

        $content['services'] = array_replace_recursive(
            config('ourcare_v2.services', []),
            $content['services'] ?? []
        );

        return $content;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::all();

        foreach (explode('.', $key) as $segment) {
            if (! is_array($value) || ! array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        CmsSetting::updateOrCreate(
            ['key' => $key],
            ['value' => json_encode($value, JSON_PRETTY_PRINT)]
        );
    }

    public static function reset(string $key): void
    {
        CmsSetting::where('key', $key)->delete();
    }

    public static function page(string $slug): array
    {
        $pages = static::get('pages', []);

        return is_array($pages) && isset($pages[$slug]) ? $pages[$slug] : [];
    }

    public static function services(): array
    {
        return static::get('services', config('ourcare_v2.services', []));
    }
}
