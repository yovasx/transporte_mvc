<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Registrar Nuevo Usuario</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/dashboard">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/usuarios">Usuarios</a></li>
                    <li class="breadcrumb-item active">Registrar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <!-- Card del Formulario -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Datos del Usuario</h3>
                <div class="card-tools">
                    <a href="<?php echo BASE_URL; ?>/usuarios" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Volver a la lista
                    </a>
                </div>
            </div>
            <form action="<?php echo BASE_URL; ?>/usuarios/crear" method="post" id="usuarioForm">
                <div class="card-body">
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="icon fas fa-ban"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?php echo isset($form_data['nombre']) ? htmlspecialchars($form_data['nombre']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="correo">Correo Electrónico *</label>
                                <input type="email" class="form-control" id="correo" name="correo" 
                                       value="<?php echo isset($form_data['correo']) ? htmlspecialchars($form_data['correo']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido_paterno">Apellido Paterno *</label>
                                <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" 
                                       value="<?php echo isset($form_data['apellido_paterno']) ? htmlspecialchars($form_data['apellido_paterno']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido_materno">Apellido Materno</label>
                                <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" 
                                       value="<?php echo isset($form_data['apellido_materno']) ? htmlspecialchars($form_data['apellido_materno']) : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Contraseña *</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       minlength="6" required>
                                <small class="form-text text-muted">Mínimo 6 caracteres</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirm_password">Confirmar Contraseña *</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" 
                                       value="<?php echo isset($form_data['telefono']) ? htmlspecialchars($form_data['telefono']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rol">Rol *</label>
                                <select class="form-control" id="rol" name="rol" required>
                                    <option value="">Seleccionar rol</option>
                                    <option value="usuario" <?php echo (isset($form_data['rol']) && $form_data['rol'] == 'usuario') ? 'selected' : ''; ?>>Usuario</option>
                                    <option value="admin" <?php echo (isset($form_data['rol']) && $form_data['rol'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                    <option value="conductor" <?php echo (isset($form_data['rol']) && $form_data['rol'] == 'conductor') ? 'selected' : ''; ?>>Conductor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="3"><?php echo isset($form_data['direccion']) ? htmlspecialchars($form_data['direccion']) : ''; ?></textarea>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Registrar Usuario
                    </button>
                    <a href="<?php echo BASE_URL; ?>/usuarios" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>

    </div>
</section>

<script>
document.getElementById('usuarioForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Las contraseñas no coinciden. Por favor, verifique.');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 6 caracteres.');
        return false;
    }
});
</script>