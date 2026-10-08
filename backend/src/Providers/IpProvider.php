<?php
declare(strict_types=1);

namespace IranDaily\Providers;

use IranDaily\Cache;
use IranDaily\Http;

/**
 * Iran-Daily — IP & Geolocation Provider
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */
final class IpProvider
{
    public static function handle(): array
    {
        $config = require __DIR__ . '/../../config/providers.php';
        $cfg    = $config['ip'];
        $cache  = new Cache(__DIR__ . '/../../storage/cache');

        return $cache->remember('ip', $cfg['ttl'], function () use ($cfg) {
            $ipData = Http::getJson($cfg['primary']);
            $ip     = $ipData['ip'] ?? null;

            $geo = [];
            if ($ip) {
                $geoData = Http::getJson($cfg['geo'] . $ip);
                $geo = [
                    'country' => $geoData['country'] ?? null,
                    'city'    => $geoData['city'] ?? null,
                    'isp'     => $geoData['isp'] ?? null,
                    'timezone'=> $geoData['timezone'] ?? null,
                ];
            }

            return [
                'ip'         => $ip,
                'geo'        => $geo,
                'updated_at' => time(),
                'source'     => 'ipify + ip-api',
            ];
        });
    }
}
