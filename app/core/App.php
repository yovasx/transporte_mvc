<?php
class App {
    protected $controller = 'Dashboard';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // Controller
        if (isset($url[0]) && file_exists(APP_PATH . '/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]);
            unset($url[0]);
        }

        // Incluir el controlador
        $controllerFile = APP_PATH . '/controllers/' . $this->controller . 'Controller.php';
        
        if (!file_exists($controllerFile)) {
            die("Error: No se encuentra el controlador: " . $controllerFile);
        }

        require_once $controllerFile;
        
        $controllerName = $this->controller . 'Controller';
        
        if (!class_exists($controllerName)) {
            die("Error: La clase {$controllerName} no existe en el archivo.");
        }
        
        $this->controller = new $controllerName;

        // Method
        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        // Parameters
        $this->params = $url ? array_values($url) : [];

        // Execute
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}
?>