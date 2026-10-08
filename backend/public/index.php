<?php
declare(strict_types=1);

/**
 * Iran-Daily — API Gateway Entry Point
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */

require __DIR__ . '/../vendor/autoload.php';

use IranDaily\Router;
use IranDaily\RateLimiter;
use IranDaily\Response;
use IranDaily\Aggregator;

// ── Bootstrap ────────────────────────────────────────
$appConfig = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appConfig['timezone']);

// ── CORS ─────────────────────────────────────────────
$cors = $appConfig['cors'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
if (in_array('*', $cors['allowed_origins'], true)) {
    header("Access-Control-Allow-Origin: {$origin}");
} elseif (in_array($origin, $cors['allowed_origins'], true)) {
    header("Access-Control-Allow-Origin: {$origin}");
}
header('Access-Control-Allow-Methods: ' . implode(', ', $cors['allowed_methods']));
header('Access-Control-Allow-Headers: ' . implode(', ', $cors['allowed_headers']));
header('Access-Control-Max-Age: ' . $cors['max_age']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Rate Limiting ────────────────────────────────────
$limiter = new RateLimiter(
    $appConfig['rate_limit']['max_requests'],
    $appConfig['rate_limit']['window'],
    __DIR__ . '/../storage/cache/ratelimit'
);

// ── Router ───────────────────────────────────────────
$router = new Router();

// Global middleware: rate limiting
$router->middleware(function (string $uri, string $method) use ($limiter): bool {
    if ($method === 'OPTIONS') {
        return true;
    }

    $result = $limiter->check();
    $limiter->headers($result);

    if (!$result['allowed']) {
        Response::json([
            'error'   => 'rate_limited',
            'message' => 'Too many requests. Slow down.',
            'author'  => 'THE SAZ',
        ], 429);
        return false;
    }

    return true;
});

// ── Routes ───────────────────────────────────────────
$router->get('/api/health', function () use ($appConfig): array {
    return [
        'ok'      => true,
        'app'     => $appConfig['name'],
        'author'  => $appConfig['author'],
        'version' => $appConfig['version'],
        'ts'      => time(),
    ];
});

$router->get('/api/all', fn() => Aggregator::all());

$router->get('/api/currency', [IranDaily\Providers\CurrencyProvider::class, 'handle']);
$router->get('/api/gold',     [IranDaily\Providers\GoldProvider::class,     'handle']);
$router->get('/api/crypto',   [IranDaily\Providers\CryptoProvider::class,   'handle']);
$router->get('/api/weather',  [IranDaily\Providers\WeatherProvider::class,  'handle']);
$router->get('/api/prayer',   [IranDaily\Providers\PrayerProvider::class,   'handle']);
$router->get('/api/ip',       [IranDaily\Providers\IpProvider::class,       'handle']);

// ── Dispatch ─────────────────────────────────────────
$router->dispatch();
