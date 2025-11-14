<?php
class Ruta {
    private $conn;
    private $table = 'ruta';

    public $id_ruta;
    public $nombre_ruta;
    public $hora_inicio;
    public $hora_final;
    public $estado;
    public $id_linea;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todas las rutas ACTIVAS
    public function getAll() {
        $query = "SELECT r.*, l.nombre_linea, l.color_linea 
                 FROM " . $this->table . " r 
                 LEFT JOIN linea l ON r.id_linea = l.id_linea 
                 WHERE r.estado = 'Activo' 
                 ORDER BY r.nombre_ruta ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener rutas activas
    public function getActivas() {
        $query = "SELECT r.*, l.nombre_linea, l.color_linea 
                 FROM " . $this->table . " r 
                 LEFT JOIN linea l ON r.id_linea = l.id_linea 
                 WHERE r.estado = 'Activo' 
                 ORDER BY r.nombre_ruta ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Crear nueva ruta
    public function crear($data) {
        $query = "INSERT INTO " . $this->table . " 
                 (nombre_ruta, hora_inicio, hora_final, estado, id_linea) 
                 VALUES (:nombre_ruta, :hora_inicio, :hora_final, :estado, :id_linea)";
        
        $stmt = $this->conn->prepare($query);
        // Normalizar id_linea: si viene vacío ('') usar NULL para no violar FK
        $id_linea = (isset($data['id_linea']) && $data['id_linea'] !== '') ? $data['id_linea'] : null;
        return $stmt->execute([
            ':nombre_ruta' => $data['nombre_ruta'],
            ':hora_inicio' => $data['hora_inicio'],
            ':hora_final' => $data['hora_final'],
            ':estado' => isset($data['estado']) ? $data['estado'] : 'Activo',
            ':id_linea' => $id_linea
        ]);
    }

    // Obtener ruta por ID
    public function getById($id) {
        $query = "SELECT r.*, l.nombre_linea, l.color_linea 
                 FROM " . $this->table . " r 
                 LEFT JOIN linea l ON r.id_linea = l.id_linea 
                 WHERE r.id_ruta = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Actualizar ruta
    public function actualizar($id, $data) {
        $query = "UPDATE " . $this->table . " 
                 SET nombre_ruta = :nombre_ruta, hora_inicio = :hora_inicio, 
                     hora_final = :hora_final, estado = :estado, id_linea = :id_linea
                 WHERE id_ruta = :id";
        
        $stmt = $this->conn->prepare($query);
        // Normalizar id_linea: si viene vacío ('') usar NULL
        $id_linea = (isset($data['id_linea']) && $data['id_linea'] !== '') ? $data['id_linea'] : null;
        return $stmt->execute([
            ':nombre_ruta' => $data['nombre_ruta'],
            ':hora_inicio' => $data['hora_inicio'],
            ':hora_final' => $data['hora_final'],
            ':estado' => isset($data['estado']) ? $data['estado'] : 'Activo',
            ':id_linea' => $id_linea,
            ':id' => $id
        ]);
    }

    // Desactivar ruta (soft delete)
    public function desactivar($id) {
        $query = "UPDATE " . $this->table . " SET estado = 'Inactivo' WHERE id_ruta = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Reactivar ruta
    public function reactivar($id) {
        $query = "UPDATE " . $this->table . " SET estado = 'Activo' WHERE id_ruta = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Obtener rutas inactivas
    public function getInactivas() {
        $query = "SELECT r.*, l.nombre_linea, l.color_linea 
                 FROM " . $this->table . " r 
                 LEFT JOIN linea l ON r.id_linea = l.id_linea 
                 WHERE r.estado = 'Inactivo' 
                 ORDER BY r.nombre_ruta ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Hard delete (en caso necesario)
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_ruta = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Obtener total de rutas activas
     * @return int Total de rutas con estado 'Activo'
     */
    public function getTotalRutas() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE estado = 'Activo'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
?>