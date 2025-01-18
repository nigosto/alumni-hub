<?php
class Router
{
    private array $routes = [];

    public function register_route($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch($method, $uri)
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === strtoupper($method) && $route['path'] === $uri) {
                return $route['handler']();
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
?>