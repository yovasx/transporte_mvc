<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Crear Nueva Parada</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/paradas">Paradas</a></li>
                    <li class="breadcrumb-item active">Crear</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Datos de la Parada</h3>
                <div class="card-tools">
                    <a href="<?php echo BASE_URL; ?>/paradas" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Volver a la lista
                    </a>
                </div>
            </div>
            <form action="<?php echo BASE_URL; ?>/paradas/crear" method="post">
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><i class="icon fas fa-ban"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="nombre_parada">Nombre de la Parada *</label>
                        <input type="text" class="form-control" id="nombre_parada" name="nombre_parada" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="latitud">Latitud</label>
                                <input type="text" class="form-control" id="latitud" name="latitud" placeholder="19.4326">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="longitud">Longitud</label>
                                <input type="text" class="form-control" id="longitud" name="longitud" placeholder="-99.1332">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Crear Parada</button>
                    <a href="<?php echo BASE_URL; ?>/paradas" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</section>
