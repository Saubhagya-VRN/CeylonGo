<?php
class Router {
    private $routes = [];

    public function get($uri, $action) {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action) {
        $this->addRoute('POST', $uri, $action);
    }

    private function addRoute($method, $uri, $action) {
        $this->routes[] = [
            'method' => $method,
            'uri'    => trim($uri, '/'),
            'action' => $action
        ];
    }

    public function dispatch($requestedUri, $method) {
        // Get path without query string
        $requestedUri = parse_url($requestedUri, PHP_URL_PATH);
        $requestedUri = '/' . trim($requestedUri, '/');

        // Adjust your project folder here
        $basePath = '/Ceylon_Go/public';

        // Remove base path
        if (str_starts_with($requestedUri, $basePath)) {
            $requestedUri = substr($requestedUri, strlen($basePath));
        }

        // Normalize
        $requestedUri = trim($requestedUri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            // Convert route patterns like /post/{id}
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $route['uri']);
            $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';

            if (preg_match($pattern, $requestedUri, $matches)) {
                array_shift($matches); // Remove full match
                return $this->runAction($route['action'], $matches);
            }
        }

        http_response_code(404);
        echo "404 Not Found - No route matched.";
    }

    private function runAction($action, $params = []) {
        list($controllerName, $methodName) = explode('@', $action);
        $controllerClass = $controllerName;

        if (!class_exists($controllerClass)) {
            throw new Exception("Controller $controllerClass not found");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) {
            throw new Exception("Method $methodName not found in controller $controllerClass");
        }

        return call_user_func_array([$controller, $methodName], $params);
    }
}
