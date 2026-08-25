<?php

namespace App\Support;

use App\Models\CmsSetting;
use Illuminate\Support\Facades\Log;
use Throwable;

class CmsContent
{
    public static function all(): array
    {
        $content = config('cms');

        foreach (static::storedSettings() as $setting) {
            $stored = json_decode((string) $setting->value, true);
            $default = data_get($content, $setting->key);

            data_set(
                $content,
                $setting->key,
                is_array($default) && is_array($stored)
                    ? array_replace_recursive($default, $stored)
                    : $stored
            );
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

    private static function storedSettings(): iterable
    {
        try {
            return CmsSetting::all();
        } catch (Throwable $exception) {
            Log::warning('CMS settings could not be loaded; falling back to config defaults.', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return [];
        }
    }
}
