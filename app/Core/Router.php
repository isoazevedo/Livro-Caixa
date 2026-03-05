<?php

class Router
{
    private array $routes = [];

    public function get(string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method'     => 'GET',
            'path'       => $path,
            'controller' => $controller,
            'action'     => $action,
        ];
    }

    public function post(string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method'     => 'POST',
            'path'       => $path,
            'controller' => $controller,
            'action'     => $action,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        // Remove query string e strip base path
        $uri = strtok($uri, '?');
        $uri = '/' . trim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            $pattern = $this->buildPattern($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter(
                    $matches,
                    fn($k) => !is_int($k),
                    ARRAY_FILTER_USE_KEY
                );

                $controllerClass = $route['controller'];
                $action = $route['action'];

                require_once __DIR__ . '/../Controllers/' . $controllerClass . '.php';
                $ctrl = new $controllerClass();
                $ctrl->$action(...array_values($params));
                return;
            }
        }

        http_response_code(404);
        require __DIR__ . '/../Views/errors/404.php';
    }

    private function buildPattern(string $path): string
    {
        // Converte :param para grupos nomeados
        $pattern = preg_replace('#:(\w+)#', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}
