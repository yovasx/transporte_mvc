<?php
class UsuariosController extends Controller {

    public function index() {
        // Solo admins pueden ver la lista de usuarios
        Auth::requireAdmin();

        $usuarioModel = $this->model('Usuario');

        try {
            $usuarios = $usuarioModel->getAll();
            $data = [
                'title' => 'Gestión de Usuarios',
                'page' => 'usuarios',
                'usuarios' => $usuarios
            ];
            $this->view('usuarios/index', $data);
        } catch(Exception $e) {
            $data = [
                'title' => 'Gestión de Usuarios',
                'page' => 'usuarios',
                'usuarios' => [],
                'error' => 'Error al cargar los usuarios: ' . $e->getMessage()
            ];
            $this->view('usuarios/index', $data);
        }
    }

    public function crear() {
        // Solo admins pueden crear usuarios
        Auth::requireAdmin();

        $data = [
            'title' => 'Registrar Nuevo Usuario',
            'page' => 'usuarios'
        ];

        if($_POST) {
            $usuarioModel = $this->model('Usuario');

            // Asignar rol por defecto 'usuario' si no se especifica
            if (!isset($_POST['rol']) || empty($_POST['rol'])) {
                $_POST['rol'] = 'usuario';
            }

            // Validaciones
            $errors = $this->validarUsuario($_POST);

            if(empty($errors)) {
                try {
                    if($usuarioModel->crear($_POST)) {
                        $_SESSION['success'] = 'Usuario registrado exitosamente';
                        $this->redirect('usuarios');
                    } else {
                        $data['error'] = 'Error al registrar el usuario';
                    }
                } catch(Exception $e) {
                    $data['error'] = 'Error: ' . $e->getMessage();
                }
            } else {
                $data['error'] = implode('<br>', $errors);
                $data['form_data'] = $_POST;
            }
        }

        $this->view('usuarios/crear', $data);
    }

    public function editar($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'ID de usuario no especificado';
            $this->redirect('usuarios');
        }

        // Solo admins pueden editar usuarios
        Auth::requireAdmin();

        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->getById($id);

        if(!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado';
            $this->redirect('usuarios');
        }

        $data = [
            'title' => 'Editar Usuario',
            'page' => 'usuarios',
            'usuario' => $usuario
        ];

        if($_POST) {
            $errors = $this->validarUsuario($_POST, $id);

            if(empty($errors)) {
                try {
                    if($usuarioModel->actualizar($id, $_POST)) {
                        $_SESSION['success'] = 'Usuario actualizado exitosamente';
                        $this->redirect('usuarios');
                    } else {
                        $data['error'] = 'Error al actualizar el usuario';
                    }
                } catch(Exception $e) {
                    $data['error'] = 'Error: ' . $e->getMessage();
                }
            } else {
                $data['error'] = implode('<br>', $errors);
            }
        }

        $this->view('usuarios/editar', $data);
    }

    public function eliminar($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'ID de usuario no especificado';
            $this->redirect('usuarios');
        }

        // Solo admins pueden eliminar usuarios
        Auth::requireAdmin();

        // No permitir eliminarse a sí mismo
        if($id == $_SESSION['usuario_id']) {
            $_SESSION['error'] = 'No puedes desactivar tu propio usuario';
            $this->redirect('usuarios');
        }

        $usuarioModel = $this->model('Usuario');

        try {
            if($usuarioModel->desactivar($id)) {
                $_SESSION['success'] = 'Usuario desactivado exitosamente';
            } else {
                $_SESSION['error'] = 'Error al desactivar el usuario';
            }
        } catch(Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }

        $this->redirect('usuarios');
    }

    // Método para ver usuarios inactivos
    public function inactivos() {
        // Solo admins pueden ver usuarios inactivos
        Auth::requireAdmin();

        $usuarioModel = $this->model('Usuario');

        try {
            $usuarios = $usuarioModel->getInactivos();
            $data = [
                'title' => 'Usuarios Inactivos',
                'page' => 'usuarios',
                'usuarios' => $usuarios,
                'vista' => 'inactivos'
            ];
            $this->view('usuarios/inactivos', $data);
        } catch(Exception $e) {
            $data = [
                'title' => 'Usuarios Inactivos',
                'page' => 'usuarios',
                'usuarios' => [],
                'vista' => 'inactivos',
                'error' => 'Error al cargar los usuarios: ' . $e->getMessage()
            ];
            $this->view('usuarios/inactivos', $data);
        }
    }

    // Método para reactivar un usuario
    public function reactivar($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'ID de usuario no especificado';
            $this->redirect('usuarios/inactivos');
        }

        // Solo admins pueden reactivar usuarios
        Auth::requireAdmin();

        $usuarioModel = $this->model('Usuario');

        try {
            if($usuarioModel->reactivar($id)) {
                $_SESSION['success'] = 'Usuario reactivado exitosamente';
            } else {
                $_SESSION['error'] = 'Error al reactivar el usuario';
            }
        } catch(Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }

        $this->redirect('usuarios/inactivos');
    }

    // Método para validar datos del usuario
    /**
     * @param array $data
     * @param int|null $id
     * @return array
     */
    private function validarUsuario(array $data, $id = null) {
        $errors = [];

        // Validar nombre
        if(empty(trim($data['nombre']))) {
            $errors[] = 'El nombre es obligatorio';
        }

        // Validar apellido paterno
        if(empty(trim($data['apellido_paterno']))) {
            $errors[] = 'El apellido paterno es obligatorio';
        }

        // Validar correo
        if(empty(trim($data['correo']))) {
            $errors[] = 'El correo electrónico es obligatorio';
        } elseif(!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El formato del correo electrónico no es válido';
        } else {
            $usuarioModel = $this->model('Usuario');
            if($usuarioModel->correoExiste($data['correo'], $id)) {
                $errors[] = 'El correo electrónico ya está registrado';
            }
        }

        // Validar contraseña (solo para crear)
        if(!$id && empty(trim($data['password']))) {
            $errors[] = 'La contraseña es obligatoria';
        } elseif(!$id && strlen($data['password']) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }

        return $errors;
    }
}
?>
