<?php
class Parada {
    private $conn;
    private $table = 'parada';

    public $id_parada;
    public $nombre_parada;
    public $latitud;
    public $longitud;
    public $estado;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todas las paradas ACTIVAS
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " WHERE estado = 'Activo' ORDER BY nombre_parada ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener parada por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_parada = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Crear nueva parada
    public function crear($data) {
        $query = "INSERT INTO " . $this->table . " 
                 (nombre_parada, latitud, longitud) 
                 VALUES (:nombre_parada, :latitud, :longitud)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':nombre_parada' => $data['nombre_parada'],
            ':latitud' => $data['latitud'],
            ':longitud' => $data['longitud']
        ]);
    }

    // Actualizar parada
    public function actualizar($id, $data) {
        $query = "UPDATE " . $this->table . " 
                 SET nombre_parada = :nombre_parada, latitud = :latitud, longitud = :longitud
                 WHERE id_parada = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':nombre_parada' => $data['nombre_parada'],
            ':latitud' => $data['latitud'],
            ':longitud' => $data['longitud'],
            ':id' => $id
        ]);
    }

    // Desactivar parada (soft delete)
    public function desactivar($id) {
        $query = "UPDATE " . $this->table . " SET estado = 'Inactivo' WHERE id_parada = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Reactivar parada
    public function reactivar(int $id) {
        $query = "UPDATE " . $this->table . " SET estado = 'Activo' WHERE id_parada = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Obtener paradas inactivas
    public function getInactivas() {
        $query = "SELECT * FROM " . $this->table . " WHERE estado = 'Inactivo' ORDER BY nombre_parada ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Hard delete (en caso necesario)
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_parada = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Obtener total de paradas activas
    public function getTotalParadas() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE estado = 'Activo'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
}
?>