<?php

namespace App\Support;

/**
 * Links for the floating support button, built from config('shop.support') (.env SUPPORT_*).
 *
 * Honesty rule: in production an empty or invalid account is left out, so the site never links to
 * an account that does not exist. Outside production (or on a demo with SHOW_PLACEHOLDER_LINKS=true)
 * it is kept with a "#" link, so the design can be seen before the real accounts are set.
 *
 *   SupportLinks::all()  // [['key' => 'whatsapp', 'url' => 'https://wa.me/98912…'], …]
 */
final class SupportLinks
{
    /** @return list<array{key: string, url: string}> */
    public static function all(): array
    {
        $config = config('shop.support', []);
        $links = [];

        foreach (['whatsapp', 'telegram', 'bale'] as $key) {
            $url = self::url($key, (string) ($config[$key] ?? ''));

            if ($url !== null) {
                $links[] = ['key' => $key, 'url' => $url];
            } elseif (self::showPlaceholders()) {
                $links[] = ['key' => $key, 'url' => '#'];
            }
        }

        return $links;
    }

    /** Show not-yet-set accounts as "#" links: outside production, or on a demo site. */
    public static function showPlaceholders(): bool
    {
        return ! app()->isProduction() || (bool) config('shop.placeholder_links');
    }

    private static function url(string $key, string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if ($key === 'whatsapp') {
            // wa.me needs the international number without "+" or leading zero: 0912… → 98912…
            $mobile = Mobile::normalize($value);

            return $mobile ? 'https://wa.me/98'.substr($mobile, 1) : null;
        }

        $base = $key === 'telegram' ? 'https://t.me/' : 'https://ble.ir/';

        return $base.rawurlencode($value);
    }
}
