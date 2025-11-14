<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Editar Ruta</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/rutas">Rutas</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Editar datos de la Ruta</h3>
                <div class="card-tools">
                    <a href="<?php echo BASE_URL; ?>/rutas" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Volver a la lista
                    </a>
                </div>
            </div>
            <form action="<?php echo BASE_URL; ?>/rutas/editar/<?php echo $ruta['id_ruta']; ?>" method="post">
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><i class="icon fas fa-ban"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="nombre_ruta">Nombre de la Ruta *</label>
                        <input type="text" class="form-control" id="nombre_ruta" name="nombre_ruta" required value="<?php echo htmlspecialchars($ruta['nombre_ruta']); ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hora_inicio">Hora Inicio *</label>
                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required value="<?php echo htmlspecialchars($ruta['hora_inicio']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hora_final">Hora Final *</label>
                                <input type="time" class="form-control" id="hora_final" name="hora_final" required value="<?php echo htmlspecialchars($ruta['hora_final']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="id_linea">Línea</label>
                        <select name="id_linea" id="id_linea" class="form-control">
                            <option value="">-- Seleccionar línea --</option>
                            <?php if(isset($lineas) && $lineas->rowCount() > 0): while($l = $lineas->fetch()): ?>
                                <option value="<?php echo $l['id_linea']; ?>" <?php echo ($ruta['id_linea'] == $l['id_linea']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($l['nombre_linea']); ?></option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Actualizar Ruta</button>
                    <a href="<?php echo BASE_URL; ?>/rutas" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</section>
