<?php
class LoginController extends Controller {
    
    public function index() {
        // Si ya está logueado, redirigir al dashboard
        if(isset($_SESSION['usuario_id'])) {
            $this->redirect('dashboard');
        }
        
        $data = [
            'title' => 'Iniciar Sesión - MoviMap'
        ];
        
        $this->viewLogin('login/index', $data);
    }
    
    public function auth() {
        if($_POST) {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            
            // Validación básica
            if(empty($email) || empty($password)) {
                $_SESSION['error'] = 'Todos los campos son obligatorios';
                $this->redirect('login');
            }
            
            // Validar formato de email
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'El formato del correo electrónico no es válido';
                $this->redirect('login');
            }
            
            // Autenticación
            if($this->authenticate($email, $password)) {
                $_SESSION['success'] = '¡Bienvenido ' . $_SESSION['usuario_nombre'] . '!';
                $this->redirect('dashboard');
            } else {
                $_SESSION['error'] = 'Credenciales incorrectas. Por favor, verifique su email y contraseña.';
                $this->redirect('login');
            }
        } else {
            $this->redirect('login');
        }
    }
    
    public function logout() {
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la sesión
        session_destroy();
        
        // Redirigir al login
        $this->redirect('login');
    }
    
    private function authenticate($email, $password) {
        try {
            $usuarioModel = $this->model('Usuario');
            $usuario = $usuarioModel->autenticar($email, $password);
            
            if($usuario) {
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido_paterno'];
                $_SESSION['usuario_email'] = $usuario['correo'];
                $_SESSION['usuario_rol'] = $usuario['rol'] ?? 'usuario';
                $_SESSION['logged_in'] = true;
                $_SESSION['login_time'] = time();
                return true;
            }
            return false;
        } catch(Exception $e) {
            error_log("Error en autenticación: " . $e->getMessage());
            // Fallback a autenticación demo si hay error de BD
            return $this->authenticateDemo($email, $password);
        }
    }
    
    // Autenticación demo por si falla la base de datos
    private function authenticateDemo($email, $password) {
        $valid_users = [
            'admin@transporte.com' => password_hash('123456', PASSWORD_DEFAULT)
        ];
        
        if(isset($valid_users[$email]) && password_verify($password, $valid_users[$email])) {
            $_SESSION['usuario_id'] = 1;
            $_SESSION['usuario_nombre'] = 'Administrador Demo';
            $_SESSION['usuario_email'] = $email;
            $_SESSION['usuario_rol'] = 'admin';
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            return true;
        }
        return false;
    }
    
    protected function viewLogin($view, $data = []) {
        extract($data);
        require_once APP_PATH . '/views/' . $view . '.php';
    }
}
?>