<?php
// MANEJADOR AJAX SIMPLE

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");

include 'config.inc.php';
include 'genericasPHP.php';

// Obtener acción de varias formas
$action = '';

// 1. Intentar desde POST/GET
if (isset($_POST['action'])) {
    $action = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
}

// 2. Si no, extraer de la ruta (después del rewrite)
if (!$action) {
    $uri = $_SERVER['REQUEST_URI'];
    // Acciones posibles
    $acciones = ['CargatablaUsuarios', 'CargaUsuario', 'ActualizaUsuario', 'EliminarUsuario', 'subirimagen'];
    foreach ($acciones as $accion) {
        if (strpos($uri, $accion) !== false) {
            $action = $accion;
            break;
        }
    }
}

try {
    include 'func_datosPHP.php';
    include 'seguridad.php';

    // Ejecutar la acción
    switch ($action) {
        case 'login':
            actionLogin();
            break;
        case 'CargatablaUsuarios':
            CargatablaUsuarios();
            break;
        case 'CargaUsuario':
            CargaUsuario();
            break;
        case 'ActualizaUsuario':
            ActualizaUsuario();
            break;
        case 'EliminarUsuario':
            EliminarUsuario();
            break;
        case 'subirimagen':
            subirimagen();
            break;
        default:
            echo json_encode(['error' => 'Acción no válida o no encontrada: ' . $action]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}

exit;

// ============================================================
// LOGIN
// ============================================================
function actionLogin() {
    $usuario  = isset($_POST['usuario'])  ? trim($_POST['usuario'])  : '';
    $password = isset($_POST['password']) ? $_POST['password']       : '';

    if (empty($usuario) || empty($password)) {
        echo json_encode(['validacion' => 'error', 'error' => 'Usuario y contraseña son obligatorios']);
        return;
    }

    try {
        $db   = getConnection();
        $stmt = $db->prepare("SELECT id, nombre, email, contrasenia, rol FROM usuario WHERE (email = ? OR nombre = ?) AND activo = 1 LIMIT 1");
        $stmt->execute([$usuario, $usuario]);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        $db   = null;

        if (!$row) {
            echo json_encode(['validacion' => 'error', 'error' => 'Usuario no encontrado o inactivo']);
            return;
        }

        if (!password_verify($password, $row['contrasenia'])) {
            echo json_encode(['validacion' => 'error', 'error' => 'Contraseña incorrecta']);
            return;
        }

        session_start();
        $_SESSION['validacion']       = 'ok';
        $_SESSION['user_codigo']      = $row['id'];
        $_SESSION['user_descripcion'] = $row['nombre'];
        $_SESSION['user_email']       = $row['email'];
        $_SESSION['user_rol']         = $row['rol'];

        echo json_encode(['validacion' => 'ok']);

    } catch (Exception $e) {
        echo json_encode(['validacion' => 'error', 'error' => $e->getMessage()]);
    }
}
?>
