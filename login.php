
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Acceso — Panel de Administración</title>
    <link href="libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="libs/fontawesome-free-5.15.1-web/css/all.css" rel="stylesheet">
    <link href="libs/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            background: #eef2f7;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        /* Tarjeta central */
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 102, 179, 0.13), 0 2px 8px rgba(0,0,0,0.07);
            overflow: hidden;
        }

        /* Cabecera azul igual que el topbar del index */
        .login-header {
            background: linear-gradient(to right, #0066B3, #0084D9);
            padding: 32px 32px 24px;
            text-align: center;
            color: #fff;
        }
        .login-header .brand-icon {
            width: 56px; height: 56px;
            background: rgba(255,255,255,0.18);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
            font-size: 1.6rem;
        }
        .login-header h1 {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 0 0 4px;
        }
        .login-header p {
            font-size: 0.85rem;
            opacity: 0.8;
            margin: 0;
        }

        /* Cuerpo del formulario */
        .login-body {
            padding: 32px;
        }

        .input-group-text {
            background: #f0f5ff;
            border-right: none;
            color: #0066B3;
        }
        .input-group .form-control {
            border-left: none;
            padding-left: 0;
        }
        .input-group .form-control:focus {
            border-color: #0084D9;
            box-shadow: none;
        }
        .input-group:focus-within .input-group-text {
            border-color: #0084D9;
        }

        .btn-login {
            background: linear-gradient(to right, #0066B3, #0084D9);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 12px;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: opacity .2s;
            width: 100%;
        }
        .btn-login:hover { opacity: 0.9; color: #fff; }
        .btn-login:active { opacity: 0.8; }

        /* Pie */
        .login-footer {
            text-align: center;
            padding: 0 32px 24px;
            font-size: 0.78rem;
            color: #aaa;
        }

        /* Mostrar/ocultar contraseña */
        .toggle-pw {
            cursor: pointer;
            color: #0066B3;
            background: #f0f5ff;
            border: 1px solid #ced4da;
            border-left: none;
            padding: 0 12px;
            display: flex; align-items: center;
            border-radius: 0 4px 4px 0;
        }
        .toggle-pw:hover { color: #004a8a; }
    </style>
</head>
<body>

    <div class="login-card">

        <!-- CABECERA -->
        <div class="login-header">
            <div class="brand-icon">
                <i class="fas fa-cloud"></i>
            </div>
            <h1>GesPol</h1>
            <p>Panel de Administración</p>
        </div>

        <!-- FORMULARIO -->
        <div class="login-body">
            <div class="form-group mb-3">
                <label class="font-weight-bold small text-muted mb-1">USUARIO</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" id="usuario" name="usuario" class="form-control form-control-lg"
                           placeholder="Introduce tu usuario" autocomplete="username">
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="font-weight-bold small text-muted mb-1">CONTRASEÑA</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" id="password" name="password" class="form-control form-control-lg"
                           placeholder="Introduce tu contraseña" autocomplete="current-password">
                    <span class="toggle-pw" id="togglePw" title="Mostrar contraseña">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </span>
                </div>
            </div>

            <button id="btnconectar" class="btn-login">
                <i class="fas fa-sign-in-alt mr-2"></i>Entrar
            </button>
        </div>

        <!-- PIE -->
        <div class="login-footer">
            &copy;<?php echo date("Y"); ?> CDi Sistemas &nbsp;&mdash;&nbsp; Todos los derechos reservados
        </div>

    </div>

    <script src="libs/jquery/dist/jquery.min.js?v=1"></script>
    <script src="libs/popper.js/dist/umd/popper.min.js?v=1"></script>
    <script src="libs/bootstrap/dist/js/bootstrap.min.js?v=1"></script>
    <script src="libs/sweetalert2/dist/sweetalert2.all.min.js?v=1"></script>
    <script src="js/genericas.js?v=1"></script>
    <script src="js/login.js?v=1"></script>
    <script>
        // Toggle mostrar/ocultar contraseña
        document.getElementById('togglePw').addEventListener('click', function() {
            var pw  = document.getElementById('password');
            var ico = document.getElementById('eyeIcon');
            if (pw.type === 'password') {
                pw.type = 'text';
                ico.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pw.type = 'password';
                ico.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    </script>
</body>
</html>