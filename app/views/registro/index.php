<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        .register-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .register-box {
            width: 480px;
        }
        .register-logo {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .register-card-body {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .btn-register {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .input-group-text {
            background-color: #f8f9fa;
        }
        .form-text {
            font-size: 0.8rem;
        }
    </style>
</head>
<body class="hold-transition register-page">
<div class="register-box">
    <!-- Register Logo -->
    <div class="register-logo text-center">
        <a href="#" class="text-white">
            <i class="fas fa-bus"></i>
            <br>
            <b>Transporte</b>App
        </a>
    </div>

    <!-- Register Card -->
    <div class="card register-card-body">
        <div class="card-body register-card-body">
            <p class="login-box-msg text-center">
                <i class="fas fa-user-plus text-success mr-2"></i>
                Crear nueva cuenta
            </p>

            <!-- Mostrar mensajes -->
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="icon fas fa-ban"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="icon fas fa-check"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="icon fas fa-ban"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>/registro" method="post" id="registerForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre" class="small">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?php echo isset($form_data['nombre']) ? htmlspecialchars($form_data['nombre']) : ''; ?>" 
                                   required placeholder="Tu nombre">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="correo" class="small">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="correo" name="correo" 
                                   value="<?php echo isset($form_data['correo']) ? htmlspecialchars($form_data['correo']) : ''; ?>" 
                                   required placeholder="tu@email.com">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido_paterno" class="small">Apellido Paterno *</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" 
                                   value="<?php echo isset($form_data['apellido_paterno']) ? htmlspecialchars($form_data['apellido_paterno']) : ''; ?>" 
                                   required placeholder="Apellido paterno">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellido_materno" class="small">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" 
                                   value="<?php echo isset($form_data['apellido_materno']) ? htmlspecialchars($form_data['apellido_materno']) : ''; ?>" 
                                   placeholder="Apellido materno">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password" class="small">Contraseña *</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   minlength="6" required placeholder="Mínimo 6 caracteres">
                            <small class="form-text text-muted">Mínimo 6 caracteres</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="confirm_password" class="small">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   required placeholder="Repite tu contraseña">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono" class="small">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="<?php echo isset($form_data['telefono']) ? htmlspecialchars($form_data['telefono']) : ''; ?>" 
                                   placeholder="Número de teléfono">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="direccion" class="small">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                   value="<?php echo isset($form_data['direccion']) ? htmlspecialchars($form_data['direccion']) : ''; ?>" 
                                   placeholder="Tu dirección">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="icheck-primary">
                        <input type="checkbox" id="agreeTerms" name="terms" value="agree" required>
                        <label for="agreeTerms" class="small">
                            Acepto los <a href="#">términos y condiciones</a>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success btn-block btn-register" id="btnRegister">
                            <i class="fas fa-user-plus mr-2"></i>Registrar Cuenta
                        </button>
                    </div>
                </div>
            </form>

            <div class="text-center mt-3">
                <a href="<?php echo BASE_URL; ?>/login" class="text-primary">
                    <i class="fas fa-arrow-left mr-1"></i>Volver al inicio de sesión
                </a>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
    // Validación del formulario
    $('#registerForm').on('submit', function(e) {
        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();
        const terms = $('#agreeTerms').is(':checked');
        
        if (!terms) {
            e.preventDefault();
            alert('Debes aceptar los términos y condiciones.');
            return false;
        }
        
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
        
        // Mostrar loading en el botón
        $('#btnRegister').html('<i class="fas fa-spinner fa-spin mr-2"></i>Registrando...');
        $('#btnRegister').prop('disabled', true);
    });
    
    // Limpiar mensajes después de 5 segundos
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>

</body>
</html>