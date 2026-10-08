<?php
declare(strict_types=1);

/**
 * Iran-Daily — Data Refresh Script
 *
 * Pre-warms cache and refreshes slow-changing data.
 * Designed to run via GitHub Actions cron.
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */

require __DIR__ . '/../backend/vendor/autoload.php';

use IranDaily\Cache;
use IranDaily\Providers\{
    PrayerProvider,
    WeatherProvider,
    CurrencyProvider,
    GoldProvider,
    CryptoProvider
};

echo "╔══════════════════════════════════════╗\n";
echo "║   Iran-Daily — Data Refresh          ║\n";
echo "║   by THE SAZ · github.com/THE-SAZ    ║\n";
echo "╚══════════════════════════════════════╝\n\n";

$cache = new Cache(__DIR__ . '/../backend/storage/cache');

// Flush stale cache
$flushed = $cache->flush();
echo "▸ Flushed {$flushed} stale cache entries\n\n";

$providers = [
    'Prayer'   => PrayerProvider::class,
    'Weather'  => WeatherProvider::class,
    'Currency' => CurrencyProvider::class,
    'Gold'     => GoldProvider::class,
    'Crypto'   => CryptoProvider::class,
];

$success = 0;
$failed  = 0;

foreach ($providers as $name => $class) {
    try {
        $start = microtime(true);
        $data  = $class::handle();
        $ms    = round((microtime(true) - $start) * 1000);
        echo "  ✔ {$name} refreshed in {$ms}ms\n";
        $success++;
    } catch (\Throwable $e) {
        echo "  ✘ {$name} failed: {$e->getMessage()}\n";
        $failed++;
    }
}

echo "\n▸ Done: {$success} success, {$failed} failed\n";
echo "▸ Cache stats: " . json_encode($cache->stats()) . "\n";

exit($failed > 0 ? 1 : 0);
