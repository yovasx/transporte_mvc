<?php
class AuditController extends Controller {

    public function index() {
        // Solo administradores
        Auth::requireAdmin();

        $auditModel = $this->model('AuditLog');
        $logs = $auditModel->getAll();

        $data = [
            'title' => 'Auditoría - Registros',
            'page' => 'audit_logs',
            'logs' => $logs,
            'usuario_nombre' => $_SESSION['usuario_nombre'] ?? null
        ];

        $this->view('audit/index', $data);
    }

    public function triggers() {
        Auth::requireAdmin();

        $thModel = $this->model('TriggerHistory');
        $items = $thModel->getAll();

        $data = [
            'title' => 'Auditoría - Triggers',
            'page' => 'trigger_history',
            'items' => $items,
            'usuario_nombre' => $_SESSION['usuario_nombre'] ?? null
        ];

        $this->view('audit/triggers', $data);
    }

    // Ejecutar sincronización de triggers: llama al procedimiento almacenado sync_trigger_history
    public function sync() {
        Auth::requireAdmin();

        try {
            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare("CALL sync_trigger_history()");
            $stmt->execute();
            $_SESSION['success'] = 'Sincronización de triggers completada.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al sincronizar triggers: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '/audit/triggers');
        exit();
    }
}
?>