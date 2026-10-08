<?php
declare(strict_types=1);

/**
 * Iran-Daily — Server-Sent Events Stream
 *
 * Streams live dashboard updates to connected clients
 * using the SSE protocol. Works on any standard
 * Apache/nginx setup without WebSocket infrastructure.
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */

require __DIR__ . '/../vendor/autoload.php';

use IranDaily\Aggregator;

// ── SSE Headers ──────────────────────────────────────
header('Content-Type: text/event-stream; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');          // nginx
header('X-Powered-By: Iran-Daily by THE SAZ');

// Disable output buffering for real-time streaming
if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
}
ini_set('zlib.output_compression', '0');
ini_set('output_buffering', '0');
ini_set('implicit_flush', '1');
while (ob_get_level() > 0) {
    ob_end_flush();
}
ob_implicit_flush(true);

// ── Stream Loop ──────────────────────────────────────
$lastHash = '';
$maxDuration = 300; // 5 min max, then client reconnects
$started = time();

while (true) {
    if (connection_aborted()) {
        break;
    }

    if ((time() - $started) > $maxDuration) {
        echo "event: reconnect\n";
        echo "data: {\"reason\":\"max_duration\"}\n\n";
        flush();
        break;
    }

    try {
        $payload = Aggregator::all();
        $hash    = md5(json_encode($payload));

        if ($hash !== $lastHash) {
            $lastHash = $hash;
            echo "event: tick\n";
            echo 'data: ' . json_encode($payload, JSON_UNESCAPED_UNICODE) . "\n\n";
            flush();
        } else {
            // Heartbeat to keep connection alive
            echo ": heartbeat " . time() . "\n\n";
            flush();
        }
    } catch (\Throwable $e) {
        echo "event: error\n";
        echo 'data: ' . json_encode(['message' => $e->getMessage()]) . "\n\n";
        flush();
    }

    sleep(15);
}
