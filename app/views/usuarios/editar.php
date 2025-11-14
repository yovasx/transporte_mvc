<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Editar Usuario</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/usuarios">Usuarios</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="icon fas fa-ban"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Editar Usuario</h3>
                <div class="card-tools">
                    <a href="<?php echo BASE_URL; ?>/usuarios" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Volver a la lista
                    </a>
                </div>
            </div>

            <form action="<?php echo BASE_URL; ?>/usuarios/editar/<?php echo $usuario['id_usuario']; ?>" method="post" id="editarUsuarioForm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required
                                       value="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="correo">Correo Electrónico *</label>
                                <input type="email" class="form-control" id="correo" name="correo" required
                                       value="<?php echo htmlspecialchars($usuario['correo']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido_paterno">Apellido Paterno *</label>
                                <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required
                                       value="<?php echo htmlspecialchars($usuario['apellido_paterno']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido_materno">Apellido Materno</label>
                                <input type="text" class="form-control" id="apellido_materno" name="apellido_materno"
                                       value="<?php echo htmlspecialchars($usuario['apellido_materno']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono"
                                       value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rol">Rol *</label>
                                <select class="form-control" id="rol" name="rol" required>
                                    <option value="usuario" <?php echo ($usuario['rol'] == 'usuario') ? 'selected' : ''; ?>>Usuario</option>
                                    <option value="admin" <?php echo ($usuario['rol'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                    <option value="conductor" <?php echo ($usuario['rol'] == 'conductor' || $usuario['rol'] == 'operador') ? 'selected' : ''; ?>>Conductor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="3"><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
                    </div>

                    <p class="text-muted"><small>Si quieres cambiar la contraseña, ve a la sección correspondiente o actualiza desde el perfil.</small></p>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Actualizar Usuario</button>
                    <a href="<?php echo BASE_URL; ?>/usuarios" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>

    </div>
</section>

<script>
// (Opcional) añadir validación ligera en cliente
document.getElementById('editarUsuarioForm').addEventListener('submit', function(e){
    const correo = document.getElementById('correo').value;
    if(!correo.includes('@')){
        e.preventDefault();
        alert('Ingrese un correo válido');
        return false;
    }
});
</script>
