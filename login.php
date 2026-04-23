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
    <title>Acceso &mdash; GesPol</title>
    <link href="libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="libs/fontawesome-free-5.15.1-web/css/all.css" rel="stylesheet">
    <link href="libs/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, Arial, sans-serif;
            background: #f5f7fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(0,40,100,0.10), 0 1px 4px rgba(0,0,0,0.06);
            padding: 40px 36px 32px;
            margin: 20px;
            animation: fadeUp .35s ease both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo {
            width: 52px; height: 52px;
            background: #0079cc;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            color: #fff;
            margin: 0 auto 16px;
        }
        .login-header h1 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a2840;
            margin-bottom: 4px;
        }
        .login-header p {
            font-size: 0.83rem;
            color: #8a97aa;
        }

        .field { margin-bottom: 18px; }
        .field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 7px;
        }
        .field-inner {
            display: flex;
            align-items: center;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
        }
        .field-inner:focus-within {
            border-color: #0079cc;
            box-shadow: 0 0 0 3px rgba(0,121,204,0.10);
        }
        .field-inner .f-icon {
            padding: 0 12px;
            color: #c0cad8;
            font-size: 0.83rem;
            flex-shrink: 0;
            transition: color .2s;
        }
        .field-inner:focus-within .f-icon { color: #0079cc; }
        .field-inner input {
            flex: 1;
            border: none;
            outline: none;
            padding: 11px 0;
            font-size: 0.93rem;
            color: #1a2840;
            background: transparent;
        }
        .field-inner input::placeholder { color: #c8d0da; }
        .toggle-pw {
            background: none; border: none; outline: none;
            padding: 0 12px; cursor: pointer;
            color: #c0cad8; font-size: 0.83rem;
            transition: color .2s; flex-shrink: 0;
        }
        .toggle-pw:hover { color: #0079cc; }

        .btn-login {
            width: 100%;
            margin-top: 8px;
            padding: 11px;
            background: #0079cc;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.93rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .18s, box-shadow .18s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover  { background: #006ab5; box-shadow: 0 3px 12px rgba(0,102,179,0.25); }
        .btn-login:active { background: #005a9e; }

        .login-footer {
            text-align: center;
            font-size: 0.71rem;
            color: #c0c8d4;
            margin-top: 24px;
        }
    </style>
</head>
<body>

    <div class="login-card">

        <div class="login-header">
            <div class="login-logo"><i class="fas fa-shield-alt"></i></div>
            <h1>GesPol</h1>
            <p>Panel de administraci&oacute;n</p>
        </div>

        <div class="field">
            <label for="usuario">Usuario</label>
            <div class="field-inner">
                <i class="fas fa-user f-icon"></i>
                <input type="text" id="usuario" name="usuario"
                       placeholder="Introduce tu usuario" autocomplete="username">
            </div>
        </div>

        <div class="field">
            <label for="password">Contrase&ntilde;a</label>
            <div class="field-inner">
                <i class="fas fa-lock f-icon"></i>
                <input type="password" id="password" name="password"
                       placeholder="Introduce tu contrase&ntilde;a" autocomplete="current-password">
                <button class="toggle-pw" id="togglePw" type="button" title="Mostrar contrase&ntilde;a">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <button id="btnconectar" class="btn-login" type="button">
            <i class="fas fa-sign-in-alt"></i>
            <span>Entrar</span>
        </button>

        <div class="login-footer">
            &copy;<?php echo date("Y"); ?> CDi Sistemas &mdash; Todos los derechos reservados
        </div>

    </div>

    <script src="libs/jquery/dist/jquery.min.js?v=1"></script>
    <script src="libs/popper.js/dist/umd/popper.min.js?v=1"></script>
    <script src="libs/bootstrap/dist/js/bootstrap.min.js?v=1"></script>
    <script src="libs/sweetalert2/dist/sweetalert2.all.min.js?v=1"></script>
    <script src="js/genericas.js?v=1"></script>
    <script src="js/login.js?v=1"></script>
    <script>
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