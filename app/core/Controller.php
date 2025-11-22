<?php
class Controller {
    protected function model($model) {
        require_once APP_PATH . '/models/' . $model . '.php';
        return new $model();
    }

    protected function view($view, $data = []) {
        // Verificar si el usuario está logueado
        $this->checkAuth();
        
        extract($data);
        
        // Cargar layout de AdminLTE
        require_once APP_PATH . '/views/layout/header.php';
        require_once APP_PATH . '/views/layout/navbar.php';
        require_once APP_PATH . '/views/layout/sidebar.php';
        
        // Cargar vista específica
        require_once APP_PATH . '/views/' . $view . '.php';
        
        require_once APP_PATH . '/views/layout/footer.php';
    }

    // Método para vistas de login (sin layout normal)
    protected function viewLogin($view, $data = []) {
        extract($data);
        require_once APP_PATH . '/views/' . $view . '.php';
    }

    protected function redirect($url) {
        header('Location: ' . BASE_URL . '/' . $url);
        exit();
    }

    // Verificar autenticación
    protected function checkAuth() {
        if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $this->redirect('login');
        }
    }
}
?>