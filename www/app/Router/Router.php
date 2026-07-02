<?php
namespace Router;

class Router {
    private array $routes = []; // Armazena as rotas registradas, organizadas por método HTTP (GET, POST, etc.)

    // Regista uma rota GET
    public function get(string $path, array $action): void {
        $this->routes['GET'][$path] = $action;
    }

    // Regista uma rota POST
    public function post(string $path, array $action): void {
        $this->routes['POST'][$path] = $action;
    }

    // Executa a rota correta
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Obtém o URL limpo (ex: /login)
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        /**
         * Se a rota for a raiz (/) e houver um parâmetro de ação, redireciona para a ação correspondente.
         * Exemplo: /?action=home será tratado como /
         */
        if ($path === '/' && isset($_GET['action'])) {
            $actionParam = strtolower($_GET['action']);
            $path = $actionParam === 'home' ? '/' : '/' . $actionParam;
        }

        // Tenta encontrar a ação mapeada para este método e caminho
        $action = $this->routes[$method][$path] ?? null;

        if (!$action) { // Se não houver ação correspondente, retorna um erro 404
            http_response_code(404);
            echo "<div style='text-align:center; padding: 50px; font-family: sans-serif;'>";
            echo "<h2 style='color:#dc3545;'>404 - Página não encontrada</h2>";
            echo "<a href='/'>Voltar ao Lobby</a>";
            echo "</div>";
            return;
        }

        $controllerName = $action[0]; // Obtém o nome da classe do controlador
        $methodName = $action[1]; // Obtém o nome do método a ser chamado no controlador

        // Instancia o controlador e chama o método via Reflexão Básica
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            if (method_exists($controller, $methodName)) {
                $controller->$methodName();
            } else {
                die("Erro de MVC: O método '{$methodName}' não existe na classe '{$controllerName}'.");
            }
        } else {
            die("Erro de MVC: O controlador '{$controllerName}' não foi encontrado pelo Autoloader.");
        }
    }
}