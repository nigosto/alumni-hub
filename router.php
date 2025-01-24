<?php
class Router
{
    private array $routes = [];

    public function register_route($method, $path, $handler)
    {
        $path = preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', $path);
        $path = '#^' . $path . '$#';

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch($method, $uri)
    {
        foreach ($this->routes as $route) {
            if (strtoupper($method) === $route['method'] && preg_match($route['path'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                call_user_func($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
?>