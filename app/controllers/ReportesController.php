<?php
require_once PUBLIC_PATH . '/assets/TCPDF-main/tcpdf.php';
require_once APP_PATH . '/libraries/SimpleXLSXGen.php';

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

    public function generatePdf() {
        Auth::requireAdmin();

        $type = $_GET['type'] ?? 'all';

        // Crear PDF usando TCPDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('freesans', 'B', 16);

        if ($type === 'rutas') {
            // Reporte específico de rutas
            $pdf->Cell(0, 10, 'REPORTE DE RUTAS REGISTRADAS', 0, 1, 'C');
            $pdf->Ln(10);

            // Obtener datos de rutas
            $rutaModel = $this->model('Ruta');
            try {
                $rutasStmt = $rutaModel->getAll();
                $rutas = $rutasStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e) {
                $rutas = [];
            }

            // Encabezados de tabla
            $pdf->SetFont('courier', 'B', 12);
            $pdf->Cell(20, 10, 'ID', 1, 0, 'C');
            $pdf->Cell(80, 10, 'NOMBRE', 1, 0, 'C');
            $pdf->Cell(30, 10, 'HORA INICIO', 1, 0, 'C');
            $pdf->Cell(30, 10, 'HORA FINAL', 1, 0, 'C');
            $pdf->Cell(25, 10, 'ESTADO', 1, 1, 'C');

            // Datos de rutas
            $pdf->SetFont('courier', '', 10);
            foreach ($rutas as $ruta) {
                $pdf->Cell(20, 10, $ruta['id_ruta'], 1, 0, 'C');
                $pdf->Cell(80, 10, $ruta['nombre_ruta'], 1, 0, 'L');
                $pdf->Cell(30, 10, $ruta['hora_inicio'], 1, 0, 'C');
                $pdf->Cell(30, 10, $ruta['hora_final'], 1, 0, 'C');
                $pdf->Cell(25, 10, ucfirst($ruta['estado']), 1, 1, 'C');
            }

            $filename = 'reporte_rutas.pdf';

        } elseif ($type === 'destinos') {
            // Reporte específico de destinos
            $pdf->Cell(0, 10, 'REPORTE DE DESTINOS MAS BUSCADOS', 0, 1, 'C');
            $pdf->Ln(10);

            // Obtener datos de paradas (destinos)
            $paradaModel = $this->model('Parada');
            try {
                $paradasStmt = $paradaModel->getAll();
                $paradas = $paradasStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e) {
                $paradas = [];
            }

            // Encabezados de tabla
            $pdf->SetFont('courier', 'B', 12);
            $pdf->Cell(20, 10, 'ID', 1, 0, 'C');
            $pdf->Cell(80, 10, 'NOMBRE', 1, 0, 'C');
            $pdf->Cell(30, 10, 'LATITUD', 1, 0, 'C');
            $pdf->Cell(30, 10, 'LONGITUD', 1, 0, 'C');
            $pdf->Cell(25, 10, 'ESTADO', 1, 1, 'C');

            // Datos de paradas
            $pdf->SetFont('courier', '', 10);
            foreach ($paradas as $parada) {
                $pdf->Cell(20, 10, $parada['id_parada'], 1, 0, 'C');
                $pdf->Cell(80, 10, $parada['nombre_parada'], 1, 0, 'L');
                $pdf->Cell(30, 10, $parada['latitud'], 1, 0, 'C');
                $pdf->Cell(30, 10, $parada['longitud'], 1, 0, 'C');
                $pdf->Cell(25, 10, ucfirst($parada['estado']), 1, 1, 'C');
            }

            $filename = 'reporte_destinos.pdf';

        } elseif ($type === 'paradas') {
            // Reporte específico de paradas
            $pdf->Cell(0, 10, 'REPORTE DE PARADAS MAS CERCANAS', 0, 1, 'C');
            $pdf->Ln(10);

            // Obtener datos de paradas
            $paradaModel = $this->model('Parada');
            try {
                $paradasStmt = $paradaModel->getAll();
                $paradas = $paradasStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e) {
                $paradas = [];
            }

            // Encabezados de tabla
            $pdf->SetFont('courier', 'B', 12);
            $pdf->Cell(20, 10, 'ID', 1, 0, 'C');
            $pdf->Cell(80, 10, 'NOMBRE', 1, 0, 'C');
            $pdf->Cell(30, 10, 'LATITUD', 1, 0, 'C');
            $pdf->Cell(30, 10, 'LONGITUD', 1, 0, 'C');
            $pdf->Cell(25, 10, 'ESTADO', 1, 1, 'C');

            // Datos de paradas
            $pdf->SetFont('courier', '', 10);
            foreach ($paradas as $parada) {
                $pdf->Cell(20, 10, $parada['id_parada'], 1, 0, 'C');
                $pdf->Cell(80, 10, $parada['nombre_parada'], 1, 0, 'L');
                $pdf->Cell(30, 10, $parada['latitud'], 1, 0, 'C');
                $pdf->Cell(30, 10, $parada['longitud'], 1, 0, 'C');
                $pdf->Cell(25, 10, ucfirst($parada['estado']), 1, 1, 'C');
            }

            $filename = 'reporte_paradas.pdf';
        }

        // Limpiar buffer de salida y establecer headers
        ob_clean();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        // Generar y descargar PDF
        $pdf->Output($filename, 'D');
    }

    public function generateExcel() {
        Auth::requireAdmin();

        $type = $_GET['type'] ?? 'all';

        if ($type === 'rutas') {
            // Obtener datos de rutas
            $rutaModel = $this->model('Ruta');
            try {
                $rutasStmt = $rutaModel->getAll();
                $rutas = $rutasStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e) {
                $rutas = [];
            }

            // Crear Excel para rutas
            $xlsx = new SimpleXLSXGen();
            $xlsx->addRow(['ID', 'Nombre', 'Hora Inicio', 'Hora Final', 'Estado']);
            foreach ($rutas as $ruta) {
                $xlsx->addRow([
                    $ruta['id_ruta'],
                    $ruta['nombre_ruta'],
                    $ruta['hora_inicio'],
                    $ruta['hora_final'],
                    ucfirst($ruta['estado'])
                ]);
            }
            $xlsx->downloadAs('reporte_rutas.xlsx');

        } elseif ($type === 'destinos') {
            // Obtener datos de paradas (destinos)
            $paradaModel = $this->model('Parada');
            try {
                $paradasStmt = $paradaModel->getAll();
                $paradas = $paradasStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e) {
                $paradas = [];
            }

            // Crear Excel para destinos
            $xlsx = new SimpleXLSXGen();
            $xlsx->addRow(['ID', 'Nombre', 'Latitud', 'Longitud', 'Estado']);
            foreach ($paradas as $parada) {
                $xlsx->addRow([
                    $parada['id_parada'],
                    $parada['nombre_parada'],
                    $parada['latitud'],
                    $parada['longitud'],
                    ucfirst($parada['estado'])
                ]);
            }
            $xlsx->downloadAs('reporte_destinos.xlsx');

        } elseif ($type === 'paradas') {
            // Obtener datos de paradas
            $paradaModel = $this->model('Parada');
            try {
                $paradasStmt = $paradaModel->getAll();
                $paradas = $paradasStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(Exception $e) {
                $paradas = [];
            }

            // Crear Excel para paradas
            $xlsx = new SimpleXLSXGen();
            $xlsx->addRow(['ID', 'Nombre', 'Latitud', 'Longitud', 'Estado']);
            foreach ($paradas as $parada) {
                $xlsx->addRow([
                    $parada['id_parada'],
                    $parada['nombre_parada'],
                    $parada['latitud'],
                    $parada['longitud'],
                    ucfirst($parada['estado'])
                ]);
            }
            $xlsx->downloadAs('reporte_paradas.xlsx');
        }
    }
}
?>
