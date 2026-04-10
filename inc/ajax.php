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
?>

