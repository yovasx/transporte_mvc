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

            // Lógica básica de búsqueda (se puede expandir)
            $rutasEncontradas = [];

            if ($origen || $destino) {
                $rutas = $rutaModel->getAll();

                foreach ($rutas as $ruta) {
                    if ($ruta['estado'] == 'Activo') {
                        // Aquí iría la lógica para verificar si la ruta conecta las paradas
                        // Por ahora, incluimos todas las rutas activas
                        $rutasEncontradas[] = [
                            'id_ruta' => $ruta['id_ruta'],
                            'nombre_ruta' => $ruta['nombre_ruta'],
                            'hora_inicio' => $ruta['hora_inicio'],
                            'hora_final' => $ruta['hora_final']
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
