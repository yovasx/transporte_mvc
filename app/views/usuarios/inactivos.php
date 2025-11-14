<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Usuarios Inactivos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/usuarios">Usuarios</a></li>
                    <li class="breadcrumb-item active">Inactivos</li>
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
                <h3 class="card-title">Lista de Usuarios Inactivos</h3>
                <div class="card-tools">
                    <a href="<?php echo BASE_URL; ?>/usuarios" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Volver a Activos
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
                                        <span class="badge badge-danger">
                                            <?php echo $usuario['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button onclick="confirmarReactivacion(<?php echo $usuario['id_usuario']; ?>)" 
                                                class="btn btn-success btn-sm" title="Reactivar">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        No hay usuarios inactivos en el sistema.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<script>
function confirmarReactivacion(id) {
    if(confirm('¿Estás seguro de que deseas reactivar este usuario?')) {
        window.location.href = '<?php echo BASE_URL; ?>/usuarios/reactivar/' + id;
    }
}
</script>
