<?php
namespace App\Core;

class Router
 {
    private array $routes = [];
    private array $middlewares = [];

    public function add(string $method, string $path, $callback, array $middlewares = []) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
            'middlewares' => $middlewares
        ];
    }

    public function middleware($middleware) {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function dispatch(string $method, string $uri) {
    foreach ($this->routes as $route) 
        {
        if ($method === $route['method'] && preg_match("#^{$route['path']}$#", $uri, $matches)) {
            array_shift($matches);
            
            foreach ($route['middlewares'] as $middleware) {
                if (is_callable($middleware)) {
                    $result = call_user_func($middleware);
                    if ($result !== true) {
                        return $result;
                    }
                }
            }
            
            return call_user_func_array($route['callback'], $matches);
        }
    }
    
    http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Route not found']);
    }
}