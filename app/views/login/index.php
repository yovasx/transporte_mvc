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
        .login-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-box {
            width: 360px;
        }
        .login-logo {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .login-card-body {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .input-group-text {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <!-- Login Logo -->
    <div class="login-logo text-center">
        <a href="#" class="text-white">
            <i class="fas fa-bus"></i>
            <br>
            <b>Transporte</b>App
        </a>
    </div>

    <!-- Login Card -->
    <div class="card login-card-body">
        <div class="card-body login-card-body">
            <p class="login-box-msg text-center">
                <i class="fas fa-lock text-primary mr-2"></i>
                Inicia sesión en tu cuenta
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

            <form action="<?php echo BASE_URL; ?>/login/auth" method="post" id="loginForm">
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Contraseña" required minlength="6">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">
                                Recordar sesión
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block btn-login" id="btnLogin">
                            <i class="fas fa-sign-in-alt mr-2"></i>Entrar
                        </button>
                    </div>
                </div>
            </form>

            <div class="social-auth-links text-center mb-3">
                <p>- O -</p>
                <a href="#" class="btn btn-block btn-outline-secondary">
                    <i class="fab fa-google mr-2"></i> Ingresar con Google
                </a>
            </div>

            <p class="mb-1 text-center">
                <a href="#" class="text-primary">
                    <i class="fas fa-key mr-1"></i>Olvidé mi contraseña
                </a>
            </p>
            <p class="mb-0 text-center">
                <a href="<?php echo BASE_URL; ?>/registro" class="text-success">
                    <i class="fas fa-user-plus mr-1"></i>Registrar nueva cuenta
                </a>
            </p>
        </div>
    </div>

    <!-- Demo Credentials -->
    <div class="card mt-3">
        <div class="card-body">
            <h6 class="card-title text-center">
                <i class="fas fa-info-circle text-info mr-2"></i>Credenciales de Demo
            </h6>
            <div class="small">
                <strong>Email:</strong> admin@transporte.com<br>
                <strong>Password:</strong> 123456
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
    $('#loginForm').on('submit', function(e) {
        var email = $('input[name="email"]').val();
        var password = $('input[name="password"]').val();
        
        if(email === '' || password === '') {
            e.preventDefault();
            alert('Por favor, complete todos los campos.');
            return false;
        }
        
        // Mostrar loading en el botón
        $('#btnLogin').html('<i class="fas fa-spinner fa-spin mr-2"></i>Verificando...');
        $('#btnLogin').prop('disabled', true);
    });
    
    // Limpiar mensajes después de 5 segundos
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>

</body>
</html>