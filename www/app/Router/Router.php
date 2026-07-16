<?php
namespace Router;

class Router {
    private array $routes = [];

    public function get(string $path, array $action): void {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, array $action): void {
        $this->routes['POST'][$path] = $action;
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if ($path === '/' && isset($_GET['action'])) {
            $actionParam = strtolower($_GET['action']);
            $path = $actionParam === 'home' ? '/' : '/' . $actionParam;
        }

        $action = null;
        $params = [];

        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[^/]+)', $route);
            $pattern = "#^" . $pattern . "$#";
            
            if (preg_match($pattern, $path, $matches)) {
                $action = $handler;
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = urldecode($match);
                    }
                }
                break;
            }
        }

        if (!$action) {
            http_response_code(404);
            echo "<div style='text-align:center; padding: 50px; font-family: sans-serif;'>";
            echo "<h2 style='color:#dc3545;'>404 - Página não encontrada</h2>";
            echo "<a href='/'>Voltar ao Lobby</a>";
            echo "</div>";
            return;
        }

        $controllerName = $action[0];
        $methodName = $action[1];

        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            if (method_exists($controller, $methodName)) {
                $controller->$methodName(...array_values($params));
            } else {
                die("Erro de MVC: O método '{$methodName}' não existe na classe '{$controllerName}'.");
            }
        } else {
            die("Erro de MVC: O controlador '{$controllerName}' não foi encontrado pelo Autoloader.");
        }
    }
}