<?php
declare(strict_types=1);

/**
 * Iran-Daily — Provider Endpoints
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */

return [
    'currency' => [
        'primary'   => 'https://api.exchangerate.host/latest?base=USD&symbols=IRR,EUR,GBP,AED,TRY,CNY',
        'fallback'  => 'https://open.er-api.com/v6/latest/USD',
        'ttl'       => 120,
    ],
    'gold' => [
        'primary'   => 'https://api.metals.live/v1/spot/gold,silver',
        'fallback'  => 'https://api.gold-api.com/price/XAU',
        'ttl'       => 120,
    ],
    'crypto' => [
        'primary'   => 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,tether,binancecoin&vs_currencies=usd&include_24hr_change=true',
        'ttl'       => 60,
    ],
    'weather' => [
        'primary'   => 'https://api.open-meteo.com/v1/forecast?latitude=35.6892&longitude=51.3890&current=temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code&timezone=Asia/Tehran',
        'ttl'       => 600,
    ],
    'prayer' => [
        'primary'   => 'https://api.aladhan.com/v1/timings/',
        'method'    => 7,
        'city'      => 'Tehran',
        'country'   => 'Iran',
        'ttl'       => 3600,
    ],
    'ip' => [
        'primary'   => 'https://api.ipify.org?format=json',
        'geo'       => 'http://ip-api.com/json/',
        'ttl'       => 300,
    ],
];
