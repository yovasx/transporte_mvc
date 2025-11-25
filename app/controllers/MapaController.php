<?php
class MapaController extends Controller {

    public function index() {
        Auth::requireLogin();
        $paradaModel = $this->model('Parada');
        $rutaModel = $this->model('Ruta');

        try {
            // fetch arrays to avoid PDOStatement reuse/exhaustion in views
            $paradasStmt = $paradaModel->getAll();
            $paradas = is_object($paradasStmt) ? $paradasStmt->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch(Exception $e) {
            $paradas = [];
        }

        try {
            $rutasStmt = $rutaModel->getAll();
            $rutas = is_object($rutasStmt) ? $rutasStmt->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch(Exception $e) {
            $rutas = [];
        }

        $highlight = $_GET['route'] ?? null;

        $data = [
            'title' => 'Mapa de Rutas - MoviMap',
            'page' => 'mapa',
            'paradas' => $paradas,
            'rutas' => $rutas,
            'highlight_route' => $highlight,
            'usuario_nombre' => $_SESSION['usuario_nombre'] ?? null
        ];

        // Render different view depending on role
        if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin') {
            $this->view('dashboard/mapa', $data);
        } else {
            $this->view('usuario/mapa', $data);
        }
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
        // Optional: associate parada to a route
        $id_ruta = isset($_POST['id_ruta']) && $_POST['id_ruta'] !== '' ? $_POST['id_ruta'] : null;

        if (!$lat || !$lng) {
            echo json_encode(['status' => 'error', 'message' => 'Coordenadas requeridas']);
            return;
        }

        $paradaModel = $this->model('Parada');
        try {
            // If an id_ruta was provided, validate that the point lies on (or near) the route geometry
            if ($id_ruta) {
                $rutaModel = $this->model('Ruta');
                $ruta = $rutaModel->getById($id_ruta);
                if (!$ruta || empty($ruta['puntos'])) {
                    echo json_encode(['status' => 'error', 'message' => 'Ruta no encontrada o sin geometría']);
                    return;
                }

                $puntosRuta = json_decode($ruta['puntos'], true);
                if (!is_array($puntosRuta) || count($puntosRuta) < 2) {
                    echo json_encode(['status' => 'error', 'message' => 'Geometría de ruta inválida']);
                    return;
                }

                // helper: distance from point to segment in meters (equirectangular approx)
                $pointToSegmentMeters = function($plat, $plng, $alat, $alng, $blat, $blng) {
                    $R = 6371000.0; // meters
                    $deg2rad = M_PI / 180.0;
                    $lat0 = $plat * $deg2rad;
                    $lon0 = $plng * $deg2rad;
                    $lat1 = $alat * $deg2rad;
                    $lon1 = $alng * $deg2rad;
                    $lat2 = $blat * $deg2rad;
                    $lon2 = $blng * $deg2rad;

                    // Convert to local cartesian using equirectangular projection centered at point
                    $x1 = ($lon1 - $lon0) * cos(($lat0 + $lat1) / 2.0) * $R;
                    $y1 = ($lat1 - $lat0) * $R;
                    $x2 = ($lon2 - $lon0) * cos(($lat0 + $lat2) / 2.0) * $R;
                    $y2 = ($lat2 - $lat0) * $R;
                    $x0 = 0.0; $y0 = 0.0;

                    $dx = $x2 - $x1;
                    $dy = $y2 - $y1;
                    if (abs($dx) < 1e-9 && abs($dy) < 1e-9) {
                        // A and B are the same point
                        return sqrt(($x0 - $x1)*($x0 - $x1) + ($y0 - $y1)*($y0 - $y1));
                    }

                    // projection t of point0 onto segment AB
                    $t = (($x0 - $x1) * $dx + ($y0 - $y1) * $dy) / ($dx*$dx + $dy*$dy);
                    if ($t < 0) {
                        $cx = $x1; $cy = $y1;
                    } elseif ($t > 1) {
                        $cx = $x2; $cy = $y2;
                    } else {
                        $cx = $x1 + $t * $dx;
                        $cy = $y1 + $t * $dy;
                    }
                    return sqrt(($x0 - $cx)*($x0 - $cx) + ($y0 - $cy)*($y0 - $cy));
                };

                $onRoute = false;
                $thresholdMeters = 30.0; // allow 30 meters tolerance
                for ($i = 0; $i < count($puntosRuta) - 1; $i++) {
                    $a = $puntosRuta[$i];
                    $b = $puntosRuta[$i+1];
                    if (!is_array($a) || !is_array($b) || count($a) < 2 || count($b) < 2) continue;
                    $d = $pointToSegmentMeters(floatval($lat), floatval($lng), floatval($a[0]), floatval($a[1]), floatval($b[0]), floatval($b[1]));
                    if ($d <= $thresholdMeters) { $onRoute = true; break; }
                }

                if (!$onRoute) {
                    echo json_encode(['status' => 'error', 'message' => 'La parada no está sobre la ruta seleccionada (distancia mayor a ' . intval($thresholdMeters) . 'm)']);
                    return;
                }
            }

            $data = [
                'nombre_parada' => $nombre,
                'latitud' => $lat,
                'longitud' => $lng
            ];
            if ($id_ruta) $data['id_ruta'] = $id_ruta;
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
            $route = $_GET['route'] ?? null;
            if ($route) {
                $paradasStmt = $paradaModel->getByRoute($route);
            } else {
                $paradasStmt = $paradaModel->getAll();
            }
            echo json_encode($paradasStmt->fetchAll(PDO::FETCH_ASSOC));
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
