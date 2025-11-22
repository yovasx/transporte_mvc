<?php
class RutasController extends Controller {
    
    public function index() {
        $rutaModel = $this->model('Ruta');
        try {
            $rutas = $rutaModel->getAll();
            $data = [
                'title' => 'Gestión de Rutas',
                'page' => 'rutas',
                'rutas' => $rutas
            ];
            $this->view('rutas/index', $data);
        } catch(Exception $e) {
            $data = [
                'title' => 'Gestión de Rutas',
                'page' => 'rutas',
                'rutas' => [],
                'error' => 'Error al cargar rutas: ' . $e->getMessage()
            ];
            $this->view('rutas/index', $data);
        }
    }

    public function crear() {
        $rutaModel = $this->model('Ruta');
        $lineaModel = $this->model('Linea');

        $data = [
            'title' => 'Crear Ruta',
            'page' => 'rutas',
            'lineas' => $lineaModel->getAll()
        ];

        if($_POST) {
            // Validaciones simples
            $errors = [];
            if(empty(trim($_POST['nombre_ruta']))) $errors[] = 'El nombre de la ruta es obligatorio';
            if(empty($_POST['hora_inicio']) || empty($_POST['hora_final'])) $errors[] = 'Debe especificar horario de inicio y fin';

            if(empty($errors)) {
                try {
                    if($rutaModel->crear($_POST)) {
                        $_SESSION['success'] = 'Ruta creada exitosamente';
                        $this->redirect('rutas');
                    } else {
                        $data['error'] = 'Error al crear la ruta';
                    }
                } catch(Exception $e) {
                    $data['error'] = 'Error: ' . $e->getMessage();
                }
            } else {
                $data['error'] = implode('<br>', $errors);
            }
        }

        $this->view('rutas/crear', $data);
    }

    public function editar($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'ID de ruta no especificado';
            $this->redirect('rutas');
        }

        $rutaModel = $this->model('Ruta');
        $lineaModel = $this->model('Linea');
        $ruta = $rutaModel->getById($id);

        if(!$ruta) {
            $_SESSION['error'] = 'Ruta no encontrada';
            $this->redirect('rutas');
        }

        $data = [
            'title' => 'Editar Ruta',
            'page' => 'rutas',
            'ruta' => $ruta,
            'lineas' => $lineaModel->getAll()
        ];

        if($_POST) {
            // Validaciones
            $errors = [];
            if(empty(trim($_POST['nombre_ruta']))) $errors[] = 'El nombre de la ruta es obligatorio';
            if(empty($_POST['hora_inicio']) || empty($_POST['hora_final'])) $errors[] = 'Debe especificar horario de inicio y fin';

            if(empty($errors)) {
                try {
                    if($rutaModel->actualizar($id, $_POST)) {
                        $_SESSION['success'] = 'Ruta actualizada exitosamente';
                        $this->redirect('rutas');
                    } else {
                        $data['error'] = 'Error al actualizar la ruta';
                    }
                } catch(Exception $e) {
                    $data['error'] = 'Error: ' . $e->getMessage();
                }
            } else {
                $data['error'] = implode('<br>', $errors);
            }
        }

        $this->view('rutas/editar', $data);
    }

    public function inactivos() {
        $rutaModel = $this->model('Ruta');
        try {
            $rutas = $rutaModel->getInactivas();
            $data = [
                'title' => 'Rutas Inactivas',
                'page' => 'rutas',
                'rutas' => $rutas
            ];
            $this->view('rutas/inactivos', $data);
        } catch(Exception $e) {
            $data = [
                'title' => 'Rutas Inactivas',
                'page' => 'rutas',
                'rutas' => [],
                'error' => 'Error al cargar rutas inactivas: ' . $e->getMessage()
            ];
            $this->view('rutas/inactivos', $data);
        }
    }

    public function eliminar($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'ID de ruta no especificado';
            $this->redirect('rutas');
        }
        $rutaModel = $this->model('Ruta');
        try {
            if($rutaModel->desactivar($id)) {
                $_SESSION['success'] = 'Ruta desactivada correctamente';
            } else {
                $_SESSION['error'] = 'Error al desactivar la ruta';
            }
        } catch(Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }
        $this->redirect('rutas');
    }

    public function reactivar($id = null) {
        if(!$id) {
            $_SESSION['error'] = 'ID de ruta no especificado';
            $this->redirect('rutas/inactivos');
        }
        $rutaModel = $this->model('Ruta');
        try {
            if($rutaModel->reactivar($id)) {
                $_SESSION['success'] = 'Ruta reactivada correctamente';
            } else {
                $_SESSION['error'] = 'Error al reactivar la ruta';
            }
        } catch(Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }
        $this->redirect('rutas/inactivos');
    }
}
?>