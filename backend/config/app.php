<?php
declare(strict_types=1);

/**
 * Iran-Daily — Application Configuration
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */

return [
    'name'          => 'Iran-Daily',
    'author'        => 'THE SAZ',
    'author_url'    => 'https://github.com/THE-SAZ',
    'version'       => '1.0.0',
    'timezone'      => 'Asia/Tehran',
    'cache_ttl'     => [
        'currency'  => 120,
        'gold'      => 120,
        'crypto'    => 60,
        'weather'   => 600,
        'prayer'    => 3600,
        'ip'        => 300,
    ],
    'rate_limit'    => [
        'max_requests' => 120,
        'window'       => 60,
    ],
    'cors'          => [
        'allowed_origins' => ['*'],
        'allowed_methods' => ['GET', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'X-Requested-With'],
        'max_age'         => 86400,
    ],
];
