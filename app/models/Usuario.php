<?php
class Usuario {
    private $conn;
    private $table = 'usuario';

    public $id_usuario;
    public $nombre;
    public $apellido_paterno;
    public $apellido_materno;
    public $correo;
    public $direccion;
    public $telefono;
    public $password;
    public $rol;
    public $estado;

    public function __construct() {
        $database = new Database();
        $this->conn = $database-> getConnection();
        // Si la conexión falló, informar claramente y detener la ejecución.
        if ($this->conn === null) {
            die("Error: no se pudo establecer la conexión a la base de datos. Revisa \"config/config.php\" y que la base de datos exista.");
        }
    }

    // Obtener todos los usuarios ACTIVOS (sin password)
    public function getAll() {
        $query = "SELECT id_usuario, nombre, apellido_paterno, apellido_materno, correo, direccion, telefono, rol, estado FROM " . $this->table . " WHERE estado = 'Activo' ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener todos los usuarios INACTIVOS
    public function getInactivos() {
        $query = "SELECT id_usuario, nombre, apellido_paterno, apellido_materno, correo, direccion, telefono, rol, estado FROM " . $this->table . " WHERE estado = 'Inactivo' ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener TODOS los usuarios sin filtro
    public function getTodos() {
        $query = "SELECT id_usuario, nombre, apellido_paterno, apellido_materno, correo, direccion, telefono, rol, estado FROM " . $this->table . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener usuario por ID (sin password)
    public function getById(int $id) {
        $query = "SELECT id_usuario, nombre, apellido_paterno, apellido_materno, correo, direccion, telefono, rol, estado FROM " . $this->table . " WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Autenticar usuario (para login) - CON VERIFICACIÓN DE CONTRASEÑA
    public function autenticar(string $correo, string $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE correo = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$correo]);

        if($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch();

            // Verificar contraseña
            if(password_verify($password, $usuario['password'])) {
                return $usuario;
            }
        }

        return false;
    }

    // Crear nuevo usuario CON CONTRASEÑA ENCRIPTADA
    public function crear(array $data) {
        // Encriptar contraseña
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        // Insertar incluyendo estado (por defecto Activo si no viene)
        $estado = isset($data['estado']) ? $data['estado'] : 'Activo';

        $query = "INSERT INTO " . $this->table . " 
                 (nombre, apellido_paterno, apellido_materno, correo, direccion, telefono, password, rol, estado) 
                 VALUES (:nombre, :apellido_paterno, :apellido_materno, :correo, :direccion, :telefono, :password, :rol, :estado)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':apellido_paterno' => $data['apellido_paterno'],
            ':apellido_materno' => $data['apellido_materno'],
            ':correo' => $data['correo'],
            ':direccion' => $data['direccion'],
            ':telefono' => $data['telefono'],
            ':password' => $password_hash,
            ':rol' => $data['rol'],
            ':estado' => $estado
        ]);
    }

    // Actualizar usuario (sin password)
    public function actualizar(int $id, array $data) {
        $query = "UPDATE " . $this->table . " 
                 SET nombre = :nombre, apellido_paterno = :apellido_paterno, 
                     apellido_materno = :apellido_materno, correo = :correo,
                     direccion = :direccion, telefono = :telefono, rol = :rol
                 WHERE id_usuario = :id";
        
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':apellido_paterno' => $data['apellido_paterno'],
            ':apellido_materno' => $data['apellido_materno'],
            ':correo' => $data['correo'],
            ':direccion' => $data['direccion'],
            ':telefono' => $data['telefono'],
            ':rol' => $data['rol'],
            ':id' => $id
        ]);
    }

    // Actualizar contraseña
    public function actualizarPassword(int $id, string $nueva_password) {
        $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);

        $query = "UPDATE " . $this->table . " SET password = :password WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':password' => $password_hash,
            ':id' => $id
        ]);
    }

    // Desactivar usuario (Soft Delete) - Cambiar estado a Inactivo
    public function desactivar(int $id) {
        $query = "UPDATE " . $this->table . " SET estado = 'Inactivo' WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Reactivar usuario
    public function reactivar(int $id) {
        $query = "UPDATE " . $this->table . " SET estado = 'Activo' WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Eliminar usuario (Hard Delete) - Solo en caso necesario
    public function eliminar(int $id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Verificar si el correo existe (excepto para un ID específico)
    public function correoExiste(string $correo, $exclude_id = null) {
        $query = "SELECT id_usuario FROM " . $this->table . " WHERE correo = ?";
        $params = [$correo];
        
        if($exclude_id) {
            $query .= " AND id_usuario != ?";
            $params[] = $exclude_id;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    // Obtener total de usuarios
    public function getTotalUsuarios() {
           $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE estado = 'Activo'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Obtener usuarios por rol
    public function getPorRol(string $rol) {
        $query = "SELECT id_usuario, nombre, apellido_paterno, apellido_materno, correo, telefono 
                 FROM " . $this->table . " 
                 WHERE rol = ? 
                 ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$rol]);
        return $stmt;
    }

    /**
     * Obtener los 5 usuarios activos más recientes
     * @return array
     */
    public function getRecientes($limite = 5) {
        $query = "SELECT id_usuario, nombre, apellido_paterno, apellido_materno, correo, rol FROM " . $this->table . " WHERE estado = 'Activo' ORDER BY id_usuario DESC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>