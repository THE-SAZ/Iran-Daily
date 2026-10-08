<?php
declare(strict_types=1);

namespace IranDaily;

use IranDaily\Providers\{
    CurrencyProvider,
    GoldProvider,
    CryptoProvider,
    WeatherProvider,
    PrayerProvider,
    IpProvider
};

/**
 * Iran-Daily — Data Aggregator
 *
 * Combines all provider outputs into a single
 * dashboard payload. Runs providers in parallel
 * where possible.
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */
final class Aggregator
{
    /**
     * Aggregate all dashboard data.
     */
    public static function all(): array
    {
        $providers = [
            'currency' => CurrencyProvider::class,
            'gold'     => GoldProvider::class,
            'crypto'   => CryptoProvider::class,
            'weather'  => WeatherProvider::class,
            'prayer'   => PrayerProvider::class,
            'ip'       => IpProvider::class,
        ];

        $results  = [];
        $errors   = [];
        $started  = microtime(true);

        foreach ($providers as $key => $class) {
            try {
                $results[$key] = $class::handle();
            } catch (\Throwable $e) {
                $errors[$key]  = $e->getMessage();
                $results[$key] = null;
            }
        }

        return [
            'meta' => [
                'app'       => 'Iran-Daily',
                'author'    => 'THE SAZ',
                'author_url'=> 'https://github.com/THE-SAZ',
                'version'   => '1.0.0',
                'ts'        => time(),
                'jalali'    => self::toJalali(new \DateTimeImmutable()),
                'elapsed'   => round((microtime(true) - $started) * 1000, 2),
                'errors'    => $errors ?: null,
            ],
            'data' => $results,
        ];
    }

    /**
     * Convert Gregorian date to Jalali string.
     */
    private static function toJalali(\DateTimeInterface $date): string
    {
        [$gy, $gm, $gd] = [
            (int) $date->format('Y'),
            (int) $date->format('n'),
            (int) $date->format('j'),
        ];

        $gDaysInMonth = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];

        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = 355666 + (365 * $gy) + intdiv($gy2 + 3, 4)
              - intdiv($gy2 + 99, 100) + intdiv($gy2 + 399, 400)
              + $gd + $gDaysInMonth[$gm - 1];

        $jy = -1595 + (33 * intdiv($days, 12053));
        $days %= 12053;
        $jy += 4 * intdiv($days, 1461);
        $days %= 1461;

        if ($days > 365) {
            $jy += intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }

        if ($days < 186) {
            $jm = 1 + intdiv($days, 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + intdiv($days - 186, 30);
            $jd = 1 + (($days - 186) % 30);
        }

        return sprintf('%04d/%02d/%02d', $jy, $jm, $jd);
    }
}
