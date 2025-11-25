<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Usuarios</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Inicio</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <!-- Mensajes de éxito/error -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fas fa-check"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fas fa-ban"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fas fa-ban"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Card Principal -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Usuarios Registrados</h3>
                <div class="card-tools">
                    <a href="<?php echo BASE_URL; ?>/usuarios/inactivos" class="btn btn-secondary btn-sm mr-2">
                        <i class="fas fa-user-slash mr-1"></i>Ver Inactivos
                    </a>
                    <a href="<?php echo BASE_URL; ?>/usuarios/crear" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus mr-1"></i>Nuevo Usuario
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if($usuarios && $usuarios->rowCount() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Completo</th>
                                    <th>Correo Electrónico</th>
                                    <th>Teléfono</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($usuario = $usuarios->fetch()): ?>
                                <tr>
                                    <td><?php echo $usuario['id_usuario']; ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['telefono'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $usuario['rol'] == 'admin' ? 'danger' : 'info'; ?>">
                                            <?php echo ucfirst($usuario['rol']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">
                                            <?php echo $usuario['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/usuarios/editar/<?php echo $usuario['id_usuario']; ?>" 
                                        class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if($usuario['id_usuario'] != $_SESSION['usuario_id']): ?>
                                        <button onclick="confirmarDesactivacion(<?php echo $usuario['id_usuario']; ?>)" 
                                                class="btn btn-danger btn-sm" title="Desactivar">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        No hay usuarios activos en el sistema.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<script>
function confirmarDesactivacion(id) {
    if(confirm('¿Estás seguro de que deseas desactivar este usuario? Podrás reactivarlo más tarde desde la sección de usuarios inactivos.')) {
        window.location.href = '<?php echo BASE_URL; ?>/usuarios/eliminar/' + id;
    }
}
</script>
