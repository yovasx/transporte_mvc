<?php
class DashboardController extends Controller {
    
    public function index() {
        // Verificar si está logueado para mostrar datos reales o demo
        $isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
        
            // Obtener datos reales de la base de datos
            $usuarioModel = $this->model('Usuario');
            $rutaModel = $this->model('Ruta');
            $paradaModel = $this->model('Parada');
        
            try {
                $totalUsuarios = $usuarioModel->getTotalUsuarios();
                $totalRutas = $rutaModel->getTotalRutas();
                $totalParadas = $paradaModel->getTotalParadas();
                $rutasActivas = $totalRutas; // Las rutas activas son las mismas que el total activo
            } catch(Exception $e) {
                // Valores por defecto si hay error
                $totalUsuarios = 0;
                $totalRutas = 0;
                $totalParadas = 0;
                $rutasActivas = 0;
            }

        // Obtener usuarios recientes (solo para dashboard privado)
        $usuariosRecientes = $usuarioModel->getRecientes(5);
        $data = [
            'title' => 'MoviMap - Inicio',
            'page' => 'dashboard',
            'isLoggedIn' => $isLoggedIn,
            'totalUsuarios' => $totalUsuarios,
            'totalRutas' => $totalRutas,
            'totalParadas' => $totalParadas,
            'rutasActivas' => $rutasActivas,
            'usuario_nombre' => $_SESSION['usuario_nombre'] ?? null,
            'usuariosRecientes' => $usuariosRecientes
        ];
        
        // Si no está logueado, usar layout público
        if(!$isLoggedIn) {
            $this->viewPublic('dashboard/public', $data);
        } else {
            $this->view('dashboard/index', $data);
        }
    }

    public function mapa() {
        // Verificar si está logueado
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $data = [
            'title' => 'Mapa de Rutas - MoviMap',
            'page' => 'mapa',
            'usuario_nombre' => $_SESSION['usuario_nombre'] ?? null
        ];

        $this->view('dashboard/mapa', $data);
    }

    // Método para vistas públicas (sin sidebar ni navbar de admin)
    protected function viewPublic($view, $data = []) {
        $data = $data ?? [];
        extract($data);
        require_once APP_PATH . '/views/layout/header_public.php';
        require_once APP_PATH . '/views/' . $view . '.php';
        require_once APP_PATH . '/views/layout/footer_public.php';
    }
}
?>