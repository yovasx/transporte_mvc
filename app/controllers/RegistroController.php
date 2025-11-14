<?php
class RegistroController extends Controller {
    
    public function index() {
        // Si ya está logueado, redirigir al dashboard
        if(isset($_SESSION['usuario_id'])) {
            $this->redirect('dashboard');
        }
        
        $data = [
            'title' => 'Registrar Nueva Cuenta - MoviMap'
        ];
        
        if($_POST) {
            $usuarioModel = $this->model('Usuario');
            
            // Validaciones
            $errors = $this->validarRegistro($_POST);
            
            if(empty($errors)) {
                try {
                    // Por defecto, los usuarios registrados desde el login son "usuario"
                    $_POST['rol'] = 'usuario';
                    
                    if($usuarioModel->crear($_POST)) {
                        $_SESSION['success'] = '¡Cuenta creada exitosamente! Ya puedes iniciar sesión.';
                        $this->redirect('login');
                    } else {
                        $data['error'] = 'Error al crear la cuenta. Por favor, intenta nuevamente.';
                    }
                } catch(Exception $e) {
                    $data['error'] = 'Error: ' . $e->getMessage();
                }
            } else {
                $data['error'] = implode('<br>', $errors);
                $data['form_data'] = $_POST;
            }
        }
        
        $this->viewLogin('registro/index', $data);
    }
    
    // Método para validar registro
    /**
     * @param array $data
     * @return array
     */
    private function validarRegistro(array $data) {
        $errors = [];
        
        // Validar nombre
        if(empty(trim($data['nombre']))) {
            $errors[] = 'El nombre es obligatorio';
        } elseif(strlen(trim($data['nombre'])) < 2) {
            $errors[] = 'El nombre debe tener al menos 2 caracteres';
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
            if($usuarioModel->correoExiste($data['correo'])) {
                $errors[] = 'El correo electrónico ya está registrado';
            }
        }
        
        // Validar contraseña
        if(empty(trim($data['password']))) {
            $errors[] = 'La contraseña es obligatoria';
        } elseif(strlen($data['password']) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        // Validar confirmación de contraseña
        if($data['password'] !== $data['confirm_password']) {
            $errors[] = 'Las contraseñas no coinciden';
        }
        
        return $errors;
    }
    
    // Método para vistas de registro (sin layout normal)
    protected function viewLogin($view, $data = []) {
        extract($data);
        require_once APP_PATH . '/views/' . $view . '.php';
    }
}
?>