<?php
class Linea {
    private $conn;
    private $table = 'linea';

    public $id_linea;
    public $nombre_linea;
    public $color_linea;
    public $tramo_largo;
    public $tramo_corto;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todas las líneas
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nombre_linea ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener línea por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_linea = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>