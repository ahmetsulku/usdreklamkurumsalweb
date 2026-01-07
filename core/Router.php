<?php
/**
 * Basit Router Sınıfı
 */

class Router
{
    private array $routes = [];
    private array $params = [];
    
    /**
     * GET route ekle
     */
    public function get(string $path, string $handler): self
    {
        $this->addRoute('GET', $path, $handler);
        return $this;
    }
    
    /**
     * POST route ekle
     */
    public function post(string $path, string $handler): self
    {
        $this->addRoute('POST', $path, $handler);
        return $this;
    }
    
    /**
     * Her iki method için route ekle
     */
    public function any(string $path, string $handler): self
    {
        $this->addRoute('GET', $path, $handler);
        $this->addRoute('POST', $path, $handler);
        return $this;
    }
    
    /**
     * Route ekle
     */
    private function addRoute(string $method, string $path, string $handler): void
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }
    
    /**
     * Route dispatch
     */
    public function dispatch(string $uri, string $method): bool
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (preg_match($route['pattern'], $uri, $matches)) {
                $this->params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $this->callHandler($route['handler']);
            }
        }
        
        return false;
    }
    
    /**
     * Handler cagir
     */
    private function callHandler(string $handler): bool
    {
        if (strpos($handler, '@') === false) {
            return false;
        }
        
        list($controllerName, $methodName) = explode('@', $handler);
        
        if (!class_exists($controllerName)) {
            return false;
        }
        
        $controller = new $controllerName();
        
        if (!method_exists($controller, $methodName)) {
            return false;
        }
        
        call_user_func_array([$controller, $methodName], $this->params);
        
        return true;
    }
    
    /**
     * Parametreleri al
     */
    public function getParams(): array
    {
        return $this->params;
    }
    
    /**
     * Mevcut URI
     */
    public static function currentUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = strtok($uri, '?');
        return rtrim($uri, '/') ?: '/';
    }
}