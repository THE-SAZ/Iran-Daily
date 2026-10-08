<?php
declare(strict_types=1);

namespace IranDaily\Providers;

use IranDaily\Cache;
use IranDaily\Http;

/**
 * Iran-Daily — Gold & Precious Metals Provider
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */
final class GoldProvider
{
    public static function handle(): array
    {
        $config = require __DIR__ . '/../../config/providers.php';
        $cfg    = $config['gold'];
        $cache  = new Cache(__DIR__ . '/../../storage/cache');

        return $cache->remember('gold', $cfg['ttl'], function () use ($cfg) {
            $data = Http::getJson($cfg['primary'], $cfg['fallback']);

            $gold  = null;
            $silver = null;

            if (isset($data[0]['price'])) {
                // metals.live format
                foreach ($data as $metal) {
                    if (($metal['symbol'] ?? '') === 'XAU') $gold = $metal['price'];
                    if (($metal['symbol'] ?? '') === 'XAG') $silver = $metal['price'];
                }
            } elseif (isset($data['price'])) {
                // gold-api.com format
                $gold = $data['price'];
            }

            return [
                'gold_oz_usd'   => $gold ? round($gold, 2) : null,
                'silver_oz_usd' => $silver ? round($silver, 2) : null,
                'gold_gram_usd' => $gold ? round($gold / 31.1035, 2) : null,
                'updated_at'    => time(),
                'source'        => 'metals.live / gold-api.com',
            ];
        });
    }
}
