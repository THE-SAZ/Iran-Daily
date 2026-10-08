<?php
declare(strict_types=1);

/**
 * Iran-Daily — Health Check Endpoint
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */

require __DIR__ . '/../vendor/autoload.php';

use IranDaily\Cache;
use IranDaily\Response;

$cache = new Cache(__DIR__ . '/../storage/cache');

$checks = [
    'php_version'   => PHP_VERSION,
    'ext_curl'      => extension_loaded('curl'),
    'ext_json'      => extension_loaded('json'),
    'cache_writable'=> is_writable(__DIR__ . '/../storage/cache'),
    'cache_stats'   => $cache->stats(),
];

$allOk = $checks['ext_curl']
      && $checks['ext_json']
      && $checks['cache_writable'];

Response::json([
    'status'    => $allOk ? 'healthy' : 'degraded',
    'app'       => 'Iran-Daily',
    'author'    => 'THE SAZ',
    'author_url'=> 'https://github.com/THE-SAZ',
    'checks'    => $checks,
    'ts'        => time(),
], $allOk ? 200 : 503);
