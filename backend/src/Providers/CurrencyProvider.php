<?php
declare(strict_types=1);

namespace IranDaily\Providers;

use IranDaily\Cache;
use IranDaily\Http;

/**
 * Iran-Daily — Currency Provider
 *
 * Fetches USD, EUR, GBP, AED, TRY, CNY rates
 * against the Iranian Rial with fallback.
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */
final class CurrencyProvider
{
    public static function handle(): array
    {
        $config = require __DIR__ . '/../../config/providers.php';
        $cfg    = $config['currency'];
        $cache  = new Cache(__DIR__ . '/../../storage/cache');

        return $cache->remember('currency', $cfg['ttl'], function () use ($cfg) {
            $data = Http::getJson($cfg['primary'], $cfg['fallback']);
            $rates = $data['rates'] ?? $data['conversion_rates'] ?? [];

            return [
                'USD' => self::normalize($rates['IRR'] ?? $rates['IRR'] ?? null),
                'EUR' => self::normalize($rates['EUR'] ?? null),
                'GBP' => self::normalize($rates['GBP'] ?? null),
                'AED' => self::normalize($rates['AED'] ?? null),
                'TRY' => self::normalize($rates['TRY'] ?? null),
                'CNY' => self::normalize($rates['CNY'] ?? null),
                'updated_at' => time(),
                'source'     => 'exchangerate.host / er-api',
            ];
        });
    }

    private static function normalize(?float $value): ?float
    {
        return $value !== null ? round($value, 2) : null;
    }
}
