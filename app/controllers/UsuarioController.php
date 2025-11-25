<?php
class UsuarioController extends Controller {

    public function buscar() {
        // Verificar que sea usuario normal
        Auth::requirePermission('search_routes');

        $rutaModel = $this->model('Ruta');
        $paradaModel = $this->model('Parada');

        try {
            $rutas = $rutaModel->getAll();
            $paradas = $paradaModel->getAll();
        } catch(Exception $e) {
            $rutas = [];
            $paradas = [];
        }

        $data = [
            'title' => 'Buscar Rutas - MoviMap',
            'page' => 'buscar_rutas',
            'rutas' => $rutas,
            'paradas' => $paradas,
            'usuario_nombre' => $_SESSION['usuario_nombre'] ?? null
        ];

        $this->view('usuario/dashboard', $data);
    }

    public function buscarRutasAjax() {
        // Verificar que sea usuario normal
        Auth::requirePermission('search_routes');

        header('Content-Type: application/json');

        try {
            $origen = $_GET['origen'] ?? null;
            $destino = $_GET['destino'] ?? null;
            $hora = $_GET['hora'] ?? null;

            $rutaModel = $this->model('Ruta');
            $paradaModel = $this->model('Parada');

            // Lógica de búsqueda más precisa: si se selecciona origen/destino,
            // devolver solo las rutas que contengan esas paradas.
            $rutasEncontradas = [];

            if ($origen || $destino) {
                // Traer todas las rutas activas una vez
                $rutasStmt = $rutaModel->getAll();
                $rutasAll = is_object($rutasStmt) ? $rutasStmt->fetchAll(PDO::FETCH_ASSOC) : [];

                // Helper PHP: distance point->segment approx in meters (equirectangular)
                $pointToSegmentMeters = function($plat, $plng, $alat, $alng, $blat, $blng) {
                    $R = 6371000.0; // meters
                    $deg2rad = M_PI / 180.0;
                    $lat0 = $plat * $deg2rad;
                    $lon0 = $plng * $deg2rad;
                    $lat1 = $alat * $deg2rad;
                    $lon1 = $alng * $deg2rad;
                    $lat2 = $blat * $deg2rad;
                    $lon2 = $blng * $deg2rad;

                    $x1 = ($lon1 - $lon0) * cos(($lat0 + $lat1) / 2.0) * $R;
                    $y1 = ($lat1 - $lat0) * $R;
                    $x2 = ($lon2 - $lon0) * cos(($lat0 + $lat2) / 2.0) * $R;
                    $y2 = ($lat2 - $lat0) * $R;
                    $x0 = 0.0; $y0 = 0.0;

                    $dx = $x2 - $x1;
                    $dy = $y2 - $y1;
                    if (abs($dx) < 1e-9 && abs($dy) < 1e-9) {
                        return sqrt(($x0 - $x1)*($x0 - $x1) + ($y0 - $y1)*($y0 - $y1));
                    }
                    $t = (($x0 - $x1) * $dx + ($y0 - $y1) * $dy) / ($dx*$dx + $dy*$dy);
                    if ($t < 0) { $cx = $x1; $cy = $y1; }
                    elseif ($t > 1) { $cx = $x2; $cy = $y2; }
                    else { $cx = $x1 + $t * $dx; $cy = $y1 + $t * $dy; }
                    return sqrt(($x0 - $cx)*($x0 - $cx) + ($y0 - $cy)*($y0 - $cy));
                };

                // For origen and destino compute set of matching route IDs
                $origenRouteIds = null; // null means no restriction
                $destinoRouteIds = null;

                if ($origen) {
                    $par = $paradaModel->getById($origen);
                    if ($par) {
                        if (!empty($par['id_ruta'])) {
                            $origenRouteIds = [(int)$par['id_ruta']];
                        } else {
                            // try to find routes whose geometry passes near this parada
                            $origenRouteIds = [];
                            foreach ($rutasAll as $r) {
                                if ($r['estado'] !== 'Activo' || empty($r['puntos'])) continue;
                                $puntos = json_decode($r['puntos'], true);
                                if (!is_array($puntos) || count($puntos) < 2) continue;
                                for ($i=0;$i<count($puntos)-1;$i++) {
                                    $a = $puntos[$i]; $b = $puntos[$i+1];
                                    if (!is_array($a) || !is_array($b) || count($a)<2 || count($b)<2) continue;
                                    $d = $pointToSegmentMeters(floatval($par['latitud']), floatval($par['longitud']), floatval($a[0]), floatval($a[1]), floatval($b[0]), floatval($b[1]));
                                    if ($d <= 30.0) { $origenRouteIds[] = (int)$r['id_ruta']; break; }
                                }
                            }
                        }
                    } else {
                        // no such parada
                        $origenRouteIds = [];
                    }
                }

                if ($destino) {
                    $par = $paradaModel->getById($destino);
                    if ($par) {
                        if (!empty($par['id_ruta'])) {
                            $destinoRouteIds = [(int)$par['id_ruta']];
                        } else {
                            $destinoRouteIds = [];
                            foreach ($rutasAll as $r) {
                                if ($r['estado'] !== 'Activo' || empty($r['puntos'])) continue;
                                $puntos = json_decode($r['puntos'], true);
                                if (!is_array($puntos) || count($puntos) < 2) continue;
                                for ($i=0;$i<count($puntos)-1;$i++) {
                                    $a = $puntos[$i]; $b = $puntos[$i+1];
                                    if (!is_array($a) || !is_array($b) || count($a)<2 || count($b)<2) continue;
                                    $d = $pointToSegmentMeters(floatval($par['latitud']), floatval($par['longitud']), floatval($a[0]), floatval($a[1]), floatval($b[0]), floatval($b[1]));
                                    if ($d <= 30.0) { $destinoRouteIds[] = (int)$r['id_ruta']; break; }
                                }
                            }
                        }
                    } else {
                        $destinoRouteIds = [];
                    }
                }

                // Compute final set of route IDs satisfying both origen and destino
                $finalIds = [];
                foreach ($rutasAll as $r) {
                    if ($r['estado'] !== 'Activo') continue;
                    $id = (int)$r['id_ruta'];
                    $ok = true;
                    if (is_array($origenRouteIds)) {
                        $ok = $ok && in_array($id, $origenRouteIds);
                    }
                    if (is_array($destinoRouteIds)) {
                        $ok = $ok && in_array($id, $destinoRouteIds);
                    }
                    if ($ok) $finalIds[] = $id;
                }

                // Build result list from finalIds
                foreach ($rutasAll as $r) {
                    if (in_array((int)$r['id_ruta'], $finalIds)) {
                        $rutasEncontradas[] = [
                            'id_ruta' => $r['id_ruta'],
                            'nombre_ruta' => $r['nombre_ruta'],
                            'hora_inicio' => $r['hora_inicio'],
                            'hora_final' => $r['hora_final']
                        ];
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'rutas' => $rutasEncontradas,
                'mensaje' => count($rutasEncontradas) > 0 ?
                    "Se encontraron " . count($rutasEncontradas) . " rutas disponibles" :
                    "No se encontraron rutas para los criterios especificados"
            ]);

        } catch(Exception $e) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Error al buscar rutas: ' . $e->getMessage()
            ]);
        }
    }
}
?>
