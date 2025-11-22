<?php
class AuditLog {
    private $conn;
    private $table = 'audit_log';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        if ($this->conn === null) {
            die("Error: no se pudo establecer la conexión a la base de datos.");
        }
    }

    // Obtener todos los logs (más recientes primero)
    public function getAll($limit = null) {
        $query = "SELECT * FROM " . $this->table . " ORDER BY changed_at DESC";
        if ($limit && is_int($limit)) {
            $query .= " LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    // Opcional: obtener por tabla
    public function getByTable($tableName) {
        $query = "SELECT * FROM " . $this->table . " WHERE table_name = :t ORDER BY changed_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':t' => $tableName]);
        return $stmt->fetchAll();
    }
}
?>