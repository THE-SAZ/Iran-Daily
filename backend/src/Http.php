<?php
declare(strict_types=1);

namespace IranDaily;

/**
 * Iran-Daily — Resilient HTTP Client
 *
 * Curl-based HTTP client with retry logic,
 * timeout handling, and fallback URL support.
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */
final class Http
{
    private const MAX_RETRIES = 2;
    private const TIMEOUT     = 8;

    /**
     * Fetch JSON from a URL with automatic retry and fallback.
     */
    public static function getJson(string $url, ?string $fallback = null): ?array
    {
        $result = self::attempt($url);

        if ($result === null && $fallback !== null) {
            $result = self::attempt($fallback);
        }

        return $result;
    }

    /**
     * Attempt a single URL with retries.
     */
    private static function attempt(string $url): ?array
    {
        for ($i = 0; $i <= self::MAX_RETRIES; $i++) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_TIMEOUT         => self::TIMEOUT,
                CURLOPT_CONNECTTIMEOUT  => 4,
                CURLOPT_FOLLOWLOCATION  => true,
                CURLOPT_MAXREDIRS       => 3,
                CURLOPT_USERAGENT       => 'Iran-Daily/1.0 (+https://github.com/THE-SAZ)',
                CURLOPT_SSL_VERIFYPEER  => true,
                CURLOPT_ENCODING        => 'gzip',
            ]);

            $body = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code === 200 && is_string($body) && $body !== '') {
                $data = json_decode($body, true);
                if (is_array($data)) {
                    return $data;
                }
            }

            if ($i < self::MAX_RETRIES) {
                usleep(200_000 * ($i + 1)); // exponential-ish backoff
            }
        }

        return null;
    }
}
