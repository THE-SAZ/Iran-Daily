<?php
declare(strict_types=1);

namespace IranDaily;

/**
 * Iran-Daily — File-Based Cache
 *
 * Zero-dependency, file-based cache with TTL support.
 * Uses atomic writes to prevent race conditions.
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */
final class Cache
{
    private string $dir;

    public function __construct(string $dir)
    {
        $this->dir = rtrim($dir, '/');
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0775, true);
        }
    }

    /**
     * Get a cached value or compute and store it.
     */
    public function remember(string $key, int $ttl, callable $resolver): mixed
    {
        $file = $this->path($key);

        if ($this->isFresh($file, $ttl)) {
            $data = json_decode(file_get_contents($file), true);
            if (is_array($data)) {
                return $data;
            }
        }

        $value = $resolver();
        $this->put($key, $value);

        return $value;
    }

    /**
     * Store a value with atomic write.
     */
    public function put(string $key, mixed $value): void
    {
        $tmp = $this->path($key) . '.' . bin2hex(random_bytes(4)) . '.tmp';
        file_put_contents(
            $tmp,
            json_encode($value, JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
        rename($tmp, $this->path($key));
    }

    /**
     * Flush the entire cache directory.
     */
    public function flush(): int
    {
        $count = 0;
        foreach (glob($this->dir . '/*.json') ?: [] as $file) {
            if (unlink($file)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get cache stats.
     */
    public function stats(): array
    {
        $files = glob($this->dir . '/*.json') ?: [];
        $total = 0;
        foreach ($files as $f) {
            $total += filesize($f) ?: 0;
        }

        return [
            'entries'   => count($files),
            'size_bytes'=> $total,
            'size_human'=> self::humanSize($total),
        ];
    }

    private function path(string $key): string
    {
        return $this->dir . '/' . hash('xxh128', $key) . '.json';
    }

    private function isFresh(string $file, int $ttl): bool
    {
        return is_file($file) && (time() - filemtime($file)) < $ttl;
    }

    private static function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
