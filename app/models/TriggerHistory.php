<?php
class TriggerHistory {
    private $conn;
    private $table = 'trigger_history';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        if ($this->conn === null) {
            die("Error: no se pudo establecer la conexión a la base de datos.");
        }
    }

    public function getAll($limit = null) {
        $query = "SELECT * FROM " . $this->table . " ORDER BY applied_at DESC";
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

    public function getByTrigger($triggerName) {
        $query = "SELECT * FROM " . $this->table . " WHERE trigger_name = :n ORDER BY applied_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':n' => $triggerName]);
        return $stmt->fetchAll();
    }
}
?>