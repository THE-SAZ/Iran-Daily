<?php
declare(strict_types=1);

namespace IranDaily;

/**
 * Iran-Daily — HTTP Response Helper
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */
final class Response
{
    /**
     * Send a JSON response with proper headers.
     */
    public static function json(
        mixed $data,
        int $status = 200,
        array $extraHeaders = []
    ): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Powered-By: Iran-Daily by THE SAZ');
        header('X-Author: https://github.com/THE-SAZ');

        foreach ($extraHeaders as $name => $value) {
            header("{$name}: {$value}");
        }

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
