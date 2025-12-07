<?php
class ReportesController extends Controller {

    public function index() {
        Auth::requireAdmin();

        // Obtener datos para los reportes
        $rutaModel = $this->model('Ruta');
        $paradaModel = $this->model('Parada');
        $usuarioModel = $this->model('Usuario');

        try {
            // Reporte de rutas más consultadas (solo rutas activas)
            $rutasActivasStmt = $rutaModel->getAll();
            $rutasMasConsultadas = $rutasActivasStmt->fetchAll(PDO::FETCH_ASSOC);
            $totalRutasConsultadas = count($rutasMasConsultadas);

            // Reporte de destinos más buscados (solo paradas activas)
            $destinosActivosStmt = $paradaModel->getAll();
            $destinosMasBuscados = $destinosActivosStmt->fetchAll(PDO::FETCH_ASSOC);
            $totalDestinosBuscados = count($destinosMasBuscados);

            // Reporte de paradas más cercanas usadas (solo paradas activas)
            $paradasActivasStmt = $paradaModel->getAll();
            $paradasCercanasUsadas = $paradasActivasStmt->fetchAll(PDO::FETCH_ASSOC);
            $totalParadasCercanas = count($paradasCercanasUsadas);

        } catch(Exception $e) {
            $rutasMasConsultadas = [];
            $destinosMasBuscados = [];
            $paradasCercanasUsadas = [];
            $totalRutasConsultadas = 0;
            $totalRutasActivas = 0;
            $totalRutasInactivas = 0;
            $totalDestinosBuscados = 0;
            $totalParadasCercanas = 0;
        }

        $data = [
            'title' => 'Reportes - MoviMap',
            'page' => 'reportes',
            'rutasMasConsultadas' => $rutasMasConsultadas,
            'destinosMasBuscados' => $destinosMasBuscados,
            'paradasCercanasUsadas' => $paradasCercanasUsadas,
            'totalRutasConsultadas' => $totalRutasConsultadas,
            'totalDestinosBuscados' => $totalDestinosBuscados,
            'totalParadasCercanas' => $totalParadasCercanas,
            'usuario_nombre' => $_SESSION['usuario_nombre'] ?? null
        ];

        $this->view('reportes/index', $data);
    }
}
?>
