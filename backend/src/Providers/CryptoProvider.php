<?php
declare(strict_types=1);

namespace IranDaily\Providers;

use IranDaily\Cache;
use IranDaily\Http;

/**
 * Iran-Daily — Cryptocurrency Provider
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */
final class CryptoProvider
{
    private const COINS = [
        'bitcoin'     => 'BTC',
        'ethereum'    => 'ETH',
        'tether'      => 'USDT',
        'binancecoin' => 'BNB',
    ];

    public static function handle(): array
    {
        $config = require __DIR__ . '/../../config/providers.php';
        $cfg    = $config['crypto'];
        $cache  = new Cache(__DIR__ . '/../../storage/cache');

        return $cache->remember('crypto', $cfg['ttl'], function () use ($cfg) {
            $data = Http::getJson($cfg['primary']);

            $result = [];
            foreach (self::COINS as $id => $symbol) {
                if (isset($data[$id])) {
                    $result[$symbol] = [
                        'usd'    => $data[$id]['usd'] ?? null,
                        'change' => round($data[$id]['usd_24h_change'] ?? 0, 2),
                    ];
                }
            }

            return [
                'coins'      => $result,
                'updated_at' => time(),
                'source'     => 'coingecko',
            ];
        });
    }
}
