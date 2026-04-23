
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
    <title>Acceso — GesPol</title>
    <link href="libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="libs/fontawesome-free-5.15.1-web/css/all.css" rel="stylesheet">
    <link href="libs/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, Arial, sans-serif;
            background: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Layout: banda izquierda + tarjeta ──────── */
        .login-shell {
            display: flex;
            width: 100%;
            max-width: 860px;
            min-height: 520px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,40,100,0.15), 0 4px 16px rgba(0,0,0,0.08);
            animation: fadeUp .4s ease both;
            margin: 20px;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Banda izquierda ────────────────────────── */
        .login-aside {
            flex: 0 0 300px;
            background: linear-gradient(160deg, #0052a3 0%, #0079cc 60%, #0091e6 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
            position: relative;
            overflow: hidden;
        }
        /* línea decorativa diagonal sutil */
        .login-aside::after {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }
        .login-aside::before {
            content: '';
            position: absolute;
            bottom: -40px; left: -40px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .aside-icon {
            width: 68px; height: 68px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem;
            color: #fff;
            margin-bottom: 22px;
            position: relative; z-index: 1;
        }
        .aside-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 3px;
            text-transform: uppercase;
            text-align: center;
            position: relative; z-index: 1;
            margin-bottom: 8px;
        }
        .aside-sub {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.6);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            text-align: center;
            position: relative; z-index: 1;
        }

        /* ── Formulario (derecha) ───────────────────── */
        .login-main {
            flex: 1;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 52px 44px;
        }
        .login-main h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a2840;
            margin-bottom: 6px;
        }
        .login-main p.sub {
            font-size: 0.85rem;
            color: #8a97aa;
            margin-bottom: 32px;
        }

        /* Campos */
        .field { margin-bottom: 20px; }
        .field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 7px;
        }
        .field-inner {
            display: flex;
            align-items: center;
            border: 1.5px solid #dde3ed;
            border-radius: 9px;
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s;
            background: #fff;
        }
        .field-inner:focus-within {
            border-color: #0079cc;
            box-shadow: 0 0 0 3px rgba(0,121,204,0.12);
        }
        .field-inner .f-icon {
            padding: 0 13px;
            color: #b0bac9;
            font-size: 0.85rem;
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
        .field-inner input::placeholder { color: #c0c8d4; }
        .toggle-pw {
            background: none; border: none; outline: none;
            padding: 0 13px; cursor: pointer;
            color: #b0bac9; font-size: 0.85rem;
            transition: color .2s;
        }
        .toggle-pw:hover { color: #0079cc; }

        /* Botón */
        .btn-login {
            width: 100%;
            margin-top: 8px;
            padding: 12px;
            background: #0079cc;
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, box-shadow .2s, transform .1s;
            display: flex; align-items: center; justify-content: center; gap: 9px;
        }
        .btn-login:hover  { background: #0066b3; box-shadow: 0 4px 16px rgba(0,102,179,0.3); }
        .btn-login:active { transform: scale(.99); }

        /* Pie */
        .login-footer {
            text-align: center;
            font-size: 0.72rem;
            color: #c0c8d4;
            margin-top: 28px;
        }

        /* ── Responsive ─────────────────────────────── */
        @media (max-width: 620px) {
            .login-aside { display: none; }
            .login-shell { max-width: 400px; border-radius: 14px; }
            .login-main  { padding: 40px 28px; }
        }
    </style>
</head>
<body>

    <div class="login-shell">

        <!-- BANDA IZQUIERDA -->
        <div class="login-aside">
            <div class="aside-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="aside-title">GesPol</div>
            <div class="aside-sub">Panel de administración</div>
        </div>

        <!-- FORMULARIO -->
        <div class="login-main">
            <h2>Bienvenido</h2>
            <p class="sub">Introduce tus credenciales para acceder</p>

            <div class="field">
                <label for="usuario">Usuario</label>
                <div class="field-inner">
                    <i class="fas fa-user f-icon"></i>
                    <input type="text" id="usuario" name="usuario"
                           placeholder="Tu usuario" autocomplete="username">
                </div>
            </div>

            <div class="field">
                <label for="password">Contraseña</label>
                <div class="field-inner">
                    <i class="fas fa-lock f-icon"></i>
                    <input type="password" id="password" name="password"
                           placeholder="Tu contraseña" autocomplete="current-password">
                    <button class="toggle-pw" id="togglePw" type="button" title="Mostrar contraseña">
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
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Acceso — GesPol</title>
    <link href="libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="libs/fontawesome-free-5.15.1-web/css/all.css" rel="stylesheet">
    <link href="libs/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background: #0a1628;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* ── Fondo animado ─────────────────────────── */
        .bg-scene {
            position: fixed; inset: 0; z-index: 0; overflow: hidden;
        }
        .bg-scene::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, #0a1628 0%, #0d2548 40%, #0a3d6b 70%, #0066B3 100%);
        }
        /* Círculos decorativos difusos */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.18;
            animation: drift 12s ease-in-out infinite alternate;
        }
        .blob-1 { width: 520px; height: 520px; background: #0084D9; top: -120px; left: -100px; animation-delay: 0s; }
        .blob-2 { width: 400px; height: 400px; background: #1e90ff; bottom: -80px; right: -60px; animation-delay: -4s; }
        .blob-3 { width: 280px; height: 280px; background: #00c6ff; top: 40%; left: 55%; animation-delay: -8s; }
        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, 20px) scale(1.07); }
        }

        /* Cuadrícula sutil */
        .bg-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        /* ── Tarjeta ───────────────────────────────── */
        .login-wrap {
            position: relative; z-index: 1;
            width: 100%; max-width: 440px;
            padding: 16px;
            animation: cardIn .55s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(28px) scale(.97); }
            to   { opacity: 1; transform: translateY(0)     scale(1);  }
        }

        .login-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            box-shadow: 0 24px 64px rgba(0,0,0,0.45), 0 0 0 1px rgba(255,255,255,0.04);
            overflow: hidden;
        }

        /* ── Cabecera ──────────────────────────────── */
        .login-header {
            padding: 40px 36px 28px;
            text-align: center;
        }
        .badge-icon {
            width: 72px; height: 72px;
            margin: 0 auto 18px;
            position: relative;
            display: flex; align-items: center; justify-content: center;
        }
        .badge-icon .ring {
            position: absolute; inset: 0;
            border-radius: 50%;
            border: 2px solid rgba(0,164,255,0.45);
            animation: ringPulse 3s ease-in-out infinite;
        }
        .badge-icon .ring-outer {
            position: absolute;
            inset: -10px;
            border-radius: 50%;
            border: 1px solid rgba(0,164,255,0.18);
            animation: ringPulse 3s ease-in-out infinite .5s;
        }
        @keyframes ringPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .4; transform: scale(1.06); }
        }
        .badge-icon .inner {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0066B3, #0084D9);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.75rem;
            color: #fff;
            box-shadow: 0 8px 24px rgba(0,100,200,0.45);
        }

        .login-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .login-header p {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.5);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Separador */
        .login-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.12), transparent);
            margin: 0 36px;
        }

        /* ── Cuerpo ────────────────────────────────── */
        .login-body { padding: 32px 36px; }

        .field-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.45);
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 20px;
        }
        .input-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.35);
            font-size: 0.9rem;
            pointer-events: none;
            transition: color .2s;
        }
        .login-input {
            width: 100%;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            color: #fff;
            font-size: 0.95rem;
            padding: 13px 42px 13px 40px;
            outline: none;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }
        .login-input::placeholder { color: rgba(255,255,255,0.25); }
        .login-input:focus {
            border-color: #0084D9;
            background: rgba(0,132,217,0.1);
            box-shadow: 0 0 0 3px rgba(0,132,217,0.18);
        }
        .login-input:focus + .input-icon,
        .input-wrap:focus-within .input-icon { color: #0084D9; }

        .toggle-pw {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: rgba(255,255,255,0.3);
            font-size: 0.9rem;
            transition: color .2s;
            background: none; border: none; padding: 4px;
        }
        .toggle-pw:hover { color: rgba(255,255,255,0.7); }

        /* ── Botón ─────────────────────────────────── */
        .btn-login {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%;
            background: linear-gradient(135deg, #0066B3 0%, #0099e6 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            cursor: pointer;
            margin-top: 8px;
            transition: transform .15s, box-shadow .15s, opacity .15s;
            box-shadow: 0 6px 20px rgba(0,100,200,0.4);
            position: relative;
            overflow: hidden;
        }
        .btn-login::after {
            content: '';
            position: absolute; inset: 0;
            background: rgba(255,255,255,0);
            transition: background .2s;
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 10px 28px rgba(0,100,200,0.5); }
        .btn-login:hover::after { background: rgba(255,255,255,0.07); }
        .btn-login:active { transform: translateY(0); opacity: .9; }

        /* ── Pie ───────────────────────────────────── */
        .login-footer {
            padding: 14px 36px 22px;
            text-align: center;
            font-size: 0.72rem;
            color: rgba(255,255,255,0.22);
            letter-spacing: 0.3px;
        }

        /* Responsivo */
        @media (max-width: 480px) {
            .login-header { padding: 30px 24px 20px; }
            .login-body   { padding: 24px; }
            .login-footer { padding: 12px 24px 18px; }
            .login-divider { margin: 0 24px; }
        }
    </style>
</head>
<body>

    <!-- Fondo -->
    <div class="bg-scene">
        <div class="bg-grid"></div>
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <!-- Tarjeta -->
    <div class="login-wrap">
        <div class="login-card">

            <!-- CABECERA -->
            <div class="login-header">
                <div class="badge-icon">
                    <div class="ring-outer"></div>
                    <div class="ring"></div>
                    <div class="inner"><i class="fas fa-shield-alt"></i></div>
                </div>
                <h1>GesPol</h1>
                <p>Panel de Administración</p>
            </div>

            <div class="login-divider"></div>

            <!-- FORMULARIO -->
            <div class="login-body">
                <div>
                    <label class="field-label" for="usuario">Usuario</label>
                    <div class="input-wrap">
                        <input type="text" id="usuario" name="usuario" class="login-input"
                               placeholder="Introduce tu usuario" autocomplete="username">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div>
                    <label class="field-label" for="password">Contraseña</label>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password" class="login-input"
                               placeholder="Introduce tu contraseña" autocomplete="current-password">
                        <i class="fas fa-lock input-icon"></i>
                        <button class="toggle-pw" id="togglePw" type="button" title="Mostrar contraseña">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button id="btnconectar" class="btn-login" type="button">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Entrar</span>
                </button>
            </div>

            <!-- PIE -->
            <div class="login-footer">
                &copy;<?php echo date("Y"); ?> CDi Sistemas &nbsp;&mdash;&nbsp; Todos los derechos reservados
            </div>

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