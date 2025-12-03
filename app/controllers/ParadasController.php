<?php
class ParadasController extends Controller {
    
    public function index() {
        $paradaModel = $this->model('Parada');
        try {
            $paradas = $paradaModel->getAll();
            $data = [
                'title' => 'Gestión de Paradas',
                'page' => 'paradas',
                'paradas' => $paradas
            ];
            $this->view('paradas/index', $data);
        } catch(Exception $e) {
            $data = [
                'title' => 'Gestión de Paradas',
                'page' => 'paradas',
                'paradas' => [],
                'error' => 'Error al cargar paradas: ' . $e->getMessage()
            ];
            $this->view('paradas/index', $data);
        }
    }

    public function crear() {
        $paradaModel = $this->model('Parada');

        $data = [
            'title' => 'Crear Parada',
            'page' => 'paradas'
        ];

        if($_POST) {
            $errors = [];
            if(empty(trim($_POST['nombre_parada']))) $errors[] = 'El nombre de la parada es obligatorio';
            // lat/long pueden ser opcionales pero validar formato básico

            if(empty($errors)) {
                try {
                    if($paradaModel->crear($_POST)) {
                        $_SESSION['success'] = 'Parada creada exitosamente';
                        $this->redirect('paradas');
                    } else {
                        $data['error'] = 'Error al crear la parada';
                    }
                } catch(Exception $e) {
                    $data['error'] = 'Error: ' . $e->getMessage();
                }
            } else {
                $data['error'] = implode('<br>', $errors);
            }
        }

        $this->view('paradas/crear', $data);
    }

    public function editar($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'ID de parada no especificado';
            $this->redirect('paradas');
        }

        $paradaModel = $this->model('Parada');
        $rutaModel = $this->model('Ruta');
        $parada = $paradaModel->getById($id);

        if(!$parada) {
            $_SESSION['error'] = 'Parada no encontrada';
            $this->redirect('paradas');
        }

        $rutas = $rutaModel->getAll();

        $data = [
            'title' => 'Editar Parada',
            'page' => 'paradas',
            'parada' => $parada,
            'rutas' => $rutas
        ];

        if($_POST) {
            $errors = [];
            if(empty(trim($_POST['nombre_parada']))) $errors[] = 'El nombre de la parada es obligatorio';

            if(empty($errors)) {
                try {
                    if($paradaModel->actualizar($id, $_POST)) {
                        $_SESSION['success'] = 'Parada actualizada exitosamente';
                        $this->redirect('paradas');
                    } else {
                        $data['error'] = 'Error al actualizar la parada';
                    }
                } catch(Exception $e) {
                    $data['error'] = 'Error: ' . $e->getMessage();
                }
            } else {
                $data['error'] = implode('<br>', $errors);
            }
        }

        $this->view('paradas/editar', $data);
    }

    public function inactivos() {
        $paradaModel = $this->model('Parada');
        try {
            $paradas = $paradaModel->getInactivas();
            $data = [
                'title' => 'Paradas Inactivas',
                'page' => 'paradas',
                'paradas' => $paradas
            ];
            $this->view('paradas/inactivos', $data);
        } catch(Exception $e) {
            $data = [
                'title' => 'Paradas Inactivas',
                'page' => 'paradas',
                'paradas' => [],
                'error' => 'Error al cargar paradas inactivas: ' . $e->getMessage()
            ];
            $this->view('paradas/inactivos', $data);
        }
    }

    public function eliminar($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'ID de parada no especificado';
            $this->redirect('paradas');
        }
        $paradaModel = $this->model('Parada');
        try {
            if($paradaModel->desactivar($id)) {
                $_SESSION['success'] = 'Parada desactivada correctamente';
            } else {
                $_SESSION['error'] = 'Error al desactivar la parada';
            }
        } catch(Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }
        $this->redirect('paradas');
    }

    public function reactivar($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'ID de parada no especificado';
            $this->redirect('paradas/inactivos');
        }
        $paradaModel = $this->model('Parada');
        try {
            if($paradaModel->reactivar($id)) {
                $_SESSION['success'] = 'Parada reactivada correctamente';
            } else {
                $_SESSION['error'] = 'Error al reactivar la parada';
            }
        } catch(Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }
        $this->redirect('paradas/inactivos');
    }
}
?>