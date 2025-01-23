<?php
class Router
{
    private array $routes = [];

    public function register_route($method, $path, $handler)
    {
        // Convert {param} to a named regex group
        $path = preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', $path);
        $path = '#^' . $path . '$#'; // Wrap with delimiters

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
                // Remove numeric keys from matches, keeping only named params
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