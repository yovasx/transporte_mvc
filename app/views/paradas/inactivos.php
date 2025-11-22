<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Paradas Inactivas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/paradas">Paradas</a></li>
                    <li class="breadcrumb-item active">Inactivos</li>
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
                <h3 class="card-title">Paradas Inactivas</h3>
                <div class="card-tools">
                    <a href="<?php echo BASE_URL; ?>/paradas" class="btn btn-primary btn-sm">Volver a Activas</a>
                </div>
            </div>
            <div class="card-body">
                <?php if($paradas && $paradas->rowCount() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Latitud</th>
                                <th>Longitud</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($parada = $paradas->fetch()): ?>
                            <tr>
                                <td><?php echo $parada['id_parada']; ?></td>
                                <td><?php echo htmlspecialchars($parada['nombre_parada']); ?></td>
                                <td><?php echo htmlspecialchars($parada['latitud']); ?></td>
                                <td><?php echo htmlspecialchars($parada['longitud']); ?></td>
                                <td><span class="badge badge-danger"><?php echo $parada['estado']; ?></span></td>
                                <td>
                                    <button onclick="confirmarReactivar(<?php echo $parada['id_parada']; ?>)" class="btn btn-success btn-sm" title="Reactivar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="alert alert-info">No hay paradas inactivas.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function confirmarReactivar(id){
    if(confirm('¿Reactivar esta parada?')) {
        window.location.href = '<?php echo BASE_URL; ?>/paradas/reactivar/' + id;
    }
}
</script>
