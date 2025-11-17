<?php
class MapaController extends Controller {

    public function index() {
        Auth::requireLogin();

        $paradaModel = $this->model('Parada');
        $rutaModel = $this->model('Ruta');

        try {
            $paradas = $paradaModel->getAll();
            $rutas = $rutaModel->getAll();
        } catch(Exception $e) {
            $paradas = [];
            $rutas = [];
        }

        $data = [
            'title' => 'Mapa de Rutas - MoviMap',
            'page' => 'mapa',
            'paradas' => $paradas,
            'rutas' => $rutas,
            'usuario_nombre' => $_SESSION['usuario_nombre'] ?? null
        ];

        $this->view('dashboard/mapa', $data);
    }

    public function guardarParada() {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
            return;
        }

        $lat = $_POST['latitud'] ?? null;
        $lng = $_POST['longitud'] ?? null;
        $nombre = $_POST['nombre_parada'] ?? 'Parada sin nombre';

        if (!$lat || !$lng) {
            echo json_encode(['status' => 'error', 'message' => 'Coordenadas requeridas']);
            return;
        }

        $paradaModel = $this->model('Parada');
        try {
            $data = [
                'nombre_parada' => $nombre,
                'latitud' => $lat,
                'longitud' => $lng
            ];
            if ($paradaModel->crear($data)) {
                echo json_encode(['status' => 'ok']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar parada']);
            }
        } catch(Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function guardarRuta() {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
            return;
        }

        $nombre = $_POST['nombre'] ?? null;
        $puntos = json_decode($_POST['puntos'] ?? '[]', true);

        if (!$nombre || empty($puntos)) {
            echo json_encode(['status' => 'error', 'message' => 'Nombre y puntos requeridos']);
            return;
        }

        $rutaModel = $this->model('Ruta');
        try {
            $data = [
                'nombre_ruta' => $nombre,
                'hora_inicio' => '06:00:00',
                'hora_final' => '23:00:00',
                'puntos' => json_encode($puntos)
            ];
            if ($rutaModel->crear($data)) {
                echo json_encode(['status' => 'ok']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar ruta']);
            }
        } catch(Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getParadas() {
        Auth::requireLogin();

        $paradaModel = $this->model('Parada');
        try {
            $paradas = $paradaModel->getAll();
            echo json_encode($paradas->fetchAll(PDO::FETCH_ASSOC));
        } catch(Exception $e) {
            echo json_encode([]);
        }
    }

    public function getRutas() {
        Auth::requireLogin();

        $rutaModel = $this->model('Ruta');
        try {
            $rutas = $rutaModel->getAll();
            echo json_encode($rutas->fetchAll(PDO::FETCH_ASSOC));
        } catch(Exception $e) {
            echo json_encode([]);
        }
    }
}
