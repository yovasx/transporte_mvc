<?php
class DashboardController extends Controller {

    public function index() {
        // Verificar si está logueado
        if (!Auth::isLoggedIn()) {
            // Mostrar dashboard público
            $this->publicDashboard();
            return;
        }

        // Redirigir según rol
        if (Auth::isAdmin()) {
            $this->adminDashboard();
        } else {
            $this->usuarioDashboard();
        }
    }

    private function publicDashboard() {
        // Dashboard público - mostrar estadísticas sin login
        $usuarioModel = $this->model('Usuario');
        $rutaModel = $this->model('Ruta');
        $paradaModel = $this->model('Parada');

        try {
            $totalUsuarios = $usuarioModel->getTotalUsuarios();
            $totalRutas = $rutaModel->getTotalRutas();
            $totalParadas = $paradaModel->getTotalParadas();
            $rutasActivas = $totalRutas;
        } catch(Exception $e) {
            $totalUsuarios = 0;
            $totalRutas = 0;
            $totalParadas = 0;
            $rutasActivas = 0;
        }

        $data = [
            'title' => 'MoviMap - Gestión de Transporte',
            'totalUsuarios' => $totalUsuarios,
            'totalRutas' => $totalRutas,
            'totalParadas' => $totalParadas,
            'rutasActivas' => $rutasActivas,
            'isLoggedIn' => false
        ];

        $this->viewPublic('dashboard/public', $data);
    }

    private function adminDashboard() {
        // Obtener datos reales de la base de datos para admin
        $usuarioModel = $this->model('Usuario');
        $rutaModel = $this->model('Ruta');
        $paradaModel = $this->model('Parada');

        try {
            $totalUsuarios = $usuarioModel->getTotalUsuarios();
            $totalRutas = $rutaModel->getTotalRutas();
            $totalParadas = $paradaModel->getTotalParadas();
            $rutasActivas = $totalRutas;
        } catch(Exception $e) {
            $totalUsuarios = 0;
            $totalRutas = 0;
            $totalParadas = 0;
            $rutasActivas = 0;
        }

        $usuariosRecientes = $usuarioModel->getRecientes(5);
        $data = [
            'title' => 'Panel de Administración - MoviMap',
            'page' => 'dashboard',
            'totalUsuarios' => $totalUsuarios,
            'totalRutas' => $totalRutas,
            'totalParadas' => $totalParadas,
            'rutasActivas' => $rutasActivas,
            'usuario_nombre' => $_SESSION['usuario_nombre'] ?? null,
            'usuariosRecientes' => $usuariosRecientes,
            'rol' => 'admin'
        ];

        $this->view('dashboard/index', $data);
    }

    private function usuarioDashboard() {
        // Dashboard para usuarios normales - búsqueda de rutas
        $rutaModel = $this->model('Ruta');
        $paradaModel = $this->model('Parada');

        try {
            $rutasStmt = $rutaModel->getAll();
            $rutas = is_object($rutasStmt) ? $rutasStmt->fetchAll(PDO::FETCH_ASSOC) : [];
            $paradasStmt = $paradaModel->getAll();
            $paradas = is_object($paradasStmt) ? $paradasStmt->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch(Exception $e) {
            $rutas = [];
            $paradas = [];
        }

        $data = [
            'title' => 'Buscar Rutas - MoviMap',
            'page' => 'usuario_dashboard',
            'rutas' => $rutas,
            'paradas' => $paradas,
            'usuario_nombre' => $_SESSION['usuario_nombre'] ?? null,
            'rol' => 'usuario'
        ];

        $this->view('usuario/dashboard', $data);
    }

    public function mapa() {
        // Verificar si está logueado
        Auth::requireLogin();

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
