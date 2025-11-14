<?php
class Tarifa {
    private $conn;
    private $table = 'tarifa';

    public $id_tarifa;
    public $id_ruta;
    public $fecha_inicio;
    public $fecha_fin;
    public $monto_estudiante;
    public $monto_personalMayor;
    public $monto_personaRegular;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todas las tarifas
    public function getAll() {
        $query = "SELECT t.*, r.nombre_ruta 
                 FROM " . $this->table . " t 
                 LEFT JOIN ruta r ON t.id_ruta = r.id_ruta 
                 ORDER BY t.fecha_inicio DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>