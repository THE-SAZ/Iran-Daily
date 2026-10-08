<?php
declare(strict_types=1);

namespace IranDaily;

/**
 * Iran-Daily — Lightweight Router
 *
 * Routes incoming HTTP requests to their handlers with
 * built-in middleware support for CORS and rate limiting.
 *
 * @package   IranDaily
 * @author    THE SAZ <https://github.com/THE-SAZ>
 * @license   MIT
 */
final class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [];

    /** @var array<callable> */
    private array $middleware = [];

    /**
     * Register a GET route.
     */
    public function get(string $path, callable|array $handler): self
    {
        $this->routes['GET'][$path] = $handler;
        return $this;
    }

    /**
     * Register a middleware that runs before every route.
     */
    public function middleware(callable $fn): self
    {
        $this->middleware[] = $fn;
        return $this;
    }

    /**
     * Dispatch the current request.
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        // Run global middleware
        foreach ($this->middleware as $mw) {
            $result = $mw($uri, $method);
            if ($result === false) {
                return; // middleware halted the request
            }
        }

        $handler = $this->routes[$method][$uri] ?? null;

        if ($handler === null) {
            Response::json(['error' => 'not_found', 'path' => $uri], 404);
            return;
        }

        try {
            $data = is_array($handler) ? $handler() : $handler();
            Response::json($data);
        } catch (\Throwable $e) {
            Response::json([
                'error'   => 'internal_error',
                'message' => $e->getMessage(),
                'author'  => 'THE SAZ',
            ], 500);
        }
    }
}
