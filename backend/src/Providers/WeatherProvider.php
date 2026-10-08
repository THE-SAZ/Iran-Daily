<?php
declare(strict_types=1);

namespace IranDaily\Providers;

use IranDaily\Cache;
use IranDaily\Http;

/**
 * Iran-Daily — Weather Provider (Tehran)
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */
final class WeatherProvider
{
    private const WMO_CODES = [
        0 => 'صاف', 1 => 'عمدتاً صاف', 2 => 'نیمه‌ابری', 3 => 'ابری',
        45 => 'مه', 48 => 'مه یخی', 51 => 'نم‌نم باران',
        53 => 'باران سبک', 55 => 'باران متوسط',
        61 => 'باران', 63 => 'باران شدید', 65 => 'باران بسیار شدید',
        71 => 'برف سبک', 73 => 'برف', 75 => 'برف شدید',
        80 => 'رگبار', 81 => 'رگبار شدید', 82 => 'رگبار سیل‌آسا',
        95 => 'رعد و برق', 96 => 'رعد و برق با تگرگ',
    ];

    public static function handle(): array
    {
        $config = require __DIR__ . '/../../config/providers.php';
        $cfg    = $config['weather'];
        $cache  = new Cache(__DIR__ . '/../../storage/cache');

        return $cache->remember('weather', $cfg['ttl'], function () use ($cfg) {
            $data = Http::getJson($cfg['primary']);
            $current = $data['current'] ?? [];

            $code = (int) ($current['weather_code'] ?? 0);

            return [
                'temp_c'      => $current['temperature_2m'] ?? null,
                'humidity'    => $current['relative_humidity_2m'] ?? null,
                'wind_kmh'    => $current['wind_speed_10m'] ?? null,
                'condition'   => self::WMO_CODES[$code] ?? 'نامشخص',
                'code'        => $code,
                'city'        => 'تهران',
                'updated_at'  => time(),
                'source'      => 'open-meteo',
            ];
        });
    }
}
