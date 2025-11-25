<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Rutas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Inicio</a></li>
                    <li class="breadcrumb-item active">Rutas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Rutas Activas</h3>
                <div class="card-tools">
                    <a href="<?php echo BASE_URL; ?>/rutas/inactivos" class="btn btn-secondary btn-sm mr-2">Ver Inactivos</a>
                    <a href="<?php echo BASE_URL; ?>/rutas/crear" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i>Nuevo
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if($rutas && $rutas->rowCount() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Horario</th>
                                <th>Línea</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($ruta = $rutas->fetch()): ?>
                            <tr>
                                <td><?php echo $ruta['id_ruta']; ?></td>
                                <td><?php echo htmlspecialchars($ruta['nombre_ruta']); ?></td>
                                <td><?php echo htmlspecialchars($ruta['hora_inicio'] . ' - ' . $ruta['hora_final']); ?></td>
                                <td><?php echo htmlspecialchars($ruta['nombre_linea'] ?? 'N/A'); ?></td>
                                <td><span class="badge badge-success"><?php echo $ruta['estado']; ?></span></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/rutas/editar/<?php echo $ruta['id_ruta']; ?>" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="confirmarDesactivar(<?php echo $ruta['id_ruta']; ?>)" class="btn btn-danger btn-sm" title="Desactivar">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="alert alert-info">No hay rutas activas.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function confirmarDesactivar(id){
    if(confirm('¿Desactivar esta ruta?')) {
        window.location.href = '<?php echo BASE_URL; ?>/rutas/eliminar/' + id;
    }
}
</script>
