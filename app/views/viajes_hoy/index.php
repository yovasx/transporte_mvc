<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>viajes hoy</title>
</head>
<body>
    <h1> VIAJES HOY </h1>
</body>
</html>
=======
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Viajes de Hoy</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Inicio</a></li>
                    <li class="breadcrumb-item active">Viajes de Hoy</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filtros -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Seleccionar Ruta y Fecha</h3>
            </div>
            <form method="post" action="<?php echo BASE_URL; ?>/dashboard/viajes_hoy">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_ruta">Ruta</label>
                                <select class="form-control" id="id_ruta" name="id_ruta" required>
                                    <option value="">Seleccionar Ruta</option>
                                    <?php foreach($rutas as $ruta): ?>
                                        <option value="<?php echo $ruta['id_ruta']; ?>" <?php echo ($ruta_seleccionada == $ruta['id_ruta']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($ruta['nombre_ruta']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $fecha_seleccionada; ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i>Buscar Viajes
                    </button>
                </div>
            </form>
        </div>

        <!-- Resultados -->
        <?php if ($ruta_seleccionada): ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Viajes Registrados</h3>
                <div class="card-tools">
                    <span class="badge badge-primary">Total: <?php echo $total_viajes; ?> viajes</span>
                </div>
            </div>
            <div class="card-body">
                <?php if ($total_viajes > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Ruta</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($viajes as $viaje): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($viaje['nombre_usuario']); ?></td>
                                    <td><?php echo htmlspecialchars($viaje['nombre_ruta']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($viaje['fecha'])); ?></td>
                                    <td><?php echo $viaje['hora']; ?></td>
                                    <td>
                                        <?php
                                        switch($viaje['tipo_accion']) {
                                            case 'busqueda':
                                                echo '<span class="badge badge-info">Búsqueda de Ruta</span>';
                                                break;
                                            case 'parada_cercana':
                                                echo '<span class="badge badge-success">Parada Más Cercana</span>';
                                                break;
                                            default:
                                                echo '<span class="badge badge-secondary">' . htmlspecialchars($viaje['tipo_accion']) . '</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Sin Viajes</h5>
                        <p>No se encontraron viajes registrados para la ruta y fecha seleccionadas.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
