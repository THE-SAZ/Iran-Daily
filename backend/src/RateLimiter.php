<?php
declare(strict_types=1);

namespace IranDaily;

/**
 * Iran-Daily — Sliding Window Rate Limiter
 *
 * File-based rate limiter using a sliding window algorithm.
 * Prevents API abuse without requiring Redis or Memcached.
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */
final class RateLimiter
{
    private string $dir;

    public function __construct(
        private int $maxRequests = 120,
        private int $windowSeconds = 60,
        string $storageDir = ''
    ) {
        $this->dir = $storageDir ?: sys_get_temp_dir() . '/iran_daily_rl';
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0775, true);
        }
    }

    /**
     * Check if the current client is allowed.
     * Returns [allowed: bool, remaining: int, reset: int]
     */
    public function check(?string $ip = null): array
    {
        $ip  = $ip ?? $this->clientIp();
        $key = hash('xxh128', $ip);
        $now = time();
        $windowStart = $now - $this->windowSeconds;

        $file  = $this->dir . '/' . $key . '.json';
        $hits  = $this->loadHits($file);
        $hits  = array_filter($hits, fn(int $t) => $t > $windowStart);
        $hits  = array_values($hits);

        $remaining = max(0, $this->maxRequests - count($hits));
        $allowed   = count($hits) < $this->maxRequests;

        if ($allowed) {
            $hits[] = $now;
        }

        file_put_contents($file, json_encode($hits), LOCK_EX);

        return [
            'allowed'   => $allowed,
            'remaining' => $remaining,
            'reset'     => $now + $this->windowSeconds,
        ];
    }

    /**
     * Send rate-limit headers.
     */
    public function headers(array $result): void
    {
        header('X-RateLimit-Limit: ' . $this->maxRequests);
        header('X-RateLimit-Remaining: ' . $result['remaining']);
        header('X-RateLimit-Reset: ' . $result['reset']);
    }

    private function loadHits(string $file): array
    {
        if (!is_file($file)) {
            return [];
        }
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : [];
    }

    private function clientIp(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '127.0.0.1';
    }
}
