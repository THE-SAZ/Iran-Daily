<?php
declare(strict_types=1);

namespace IranDaily\Providers;

use IranDaily\Cache;
use IranDaily\Http;

/**
 * Iran-Daily — Prayer Times Provider (Tehran)
 *
 * Uses Aladhan API with the University of Tehran
 * calculation method (method=7).
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */
final class PrayerProvider
{
    private const PRAYERS = ['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];

    public static function handle(): array
    {
        $config = require __DIR__ . '/../../config/providers.php';
        $cfg    = $config['prayer'];
        $cache  = new Cache(__DIR__ . '/../../storage/cache');

        $cacheKey = 'prayer_' . date('Y-m-d');

        return $cache->remember($cacheKey, $cfg['ttl'], function () use ($cfg) {
            $today = date('d-m-Y');
            $url   = $cfg['primary'] . $today
                   . '?city=' . urlencode($cfg['city'])
                   . '&country=' . urlencode($cfg['country'])
                   . '&method=' . $cfg['method'];

            $data    = Http::getJson($url);
            $timings = $data['data']['timings'] ?? [];

            $result = [];
            foreach (self::PRAYERS as $name) {
                $raw = $timings[$name] ?? null;
                $result[$name] = $raw ? preg_replace('/\s*\(.*\)$/', '', $raw) : null;
            }

            return [
                'times'      => $result,
                'city'       => $cfg['city'],
                'updated_at' => time(),
                'source'     => 'aladhan.com',
            ];
        });
    }
}
