<?php

include '../config.inc.php';
include '../genericasPHP.php';
include '../func_datosPHP.php';
include 'func_plantillas.php';
include("../seguridad.php");

require '../../libs/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

$app = new \Slim\Slim();

// ============================================
// RUTAS GET - OBTENER DATOS
// ============================================

$app->get('/ListarPlantillas', 'ListarPlantillas');
$app->get('/ObtenerPlantilla', 'ObtenerPlantilla_Handler');
$app->get('/ObtenerFiltros', 'ObtenerFiltros_Handler');
$app->get('/ObtenerVariables', 'ObtenerVariables_Handler');
$app->post('/CargaTablasPlantillas', 'CargaTablasPlantillas');

// ============================================
// RUTAS POST - MANIPULAR DATOS
// ============================================

$app->post('/CrearPlantilla', 'CrearPlantilla_Handler');
$app->post('/ActualizarPlantilla', 'ActualizarPlantilla_Handler');
$app->post('/EliminarPlantilla', 'EliminarPlantilla_Handler');
$app->post('/ReemplazarVariables', 'ReemplazarVariables_Handler');
$app->post('/GuardarDocumento', 'GuardarDocumento_Handler');

$app->run();

// ============================================
// HANDLERS
// ============================================

function ListarPlantillas() {
    $tabla = "plantillas_maestro";
    $primaryKey = "cod_plantilla";
    $campos = "cod_plantilla,cod_plantilla,nombre,descripcion,tipo_documento,estado";
    $tiposcampo = "texto,texto,texto,texto,texto,numero";
    $joinQuery = "";
    $extraWhere = "estado = 1";
    
    echo CargaTablaPHP($tabla, $campos, $tiposcampo, $primaryKey, $joinQuery, $extraWhere);
}

function ObtenerPlantilla_Handler() {
    $cod = isset($_GET['cod']) ? $_GET['cod'] : '';
    
    if (empty($cod)) {
        echo json_encode(['validacion' => 'ko', 'error' => 'Código requerido']);
        return;
    }
    
    $plantilla = ObtenerPlantilla($cod);
    
    if ($plantilla) {
        $variables = json_decode(ObtenerVariables($cod), true);
        $filtros = json_decode(ObtenerFiltros($cod), true);
        
        echo json_encode([
            'validacion' => 'ok',
            'data' => array_merge($plantilla, [
                'variables' => $variables,
                'filtros' => $filtros
            ])
        ]);
    } else {
        echo json_encode(['validacion' => 'ko', 'error' => 'Plantilla no encontrada']);
    }
}

function ObtenerFiltros_Handler() {
    $cod = isset($_GET['cod']) ? $_GET['cod'] : '';
    
    if (empty($cod)) {
        echo json_encode(['validacion' => 'ko', 'error' => 'Código requerido']);
        return;
    }
    
    $filtros_json = ObtenerFiltros($cod);
    $filtros = json_decode($filtros_json, true);
    
    echo json_encode(['validacion' => 'ok', 'data' => $filtros]);
}

function ObtenerVariables_Handler() {
    $cod = isset($_GET['cod']) ? $_GET['cod'] : '';
    
    if (empty($cod)) {
        echo json_encode(['validacion' => 'ko', 'error' => 'Código requerido']);
        return;
    }
    
    $variables_json = ObtenerVariables($cod);
    $variables = json_decode($variables_json, true);
    
    echo json_encode(['validacion' => 'ok', 'data' => $variables]);
}

function CargaTablasPlantillas() {
    $tabla = "plantillas_maestro";
    $primaryKey = "cod_plantilla";
    $campos = "cod_plantilla,cod_plantilla,nombre,descripcion,tipo_documento,estado";
    $tiposcampo = "texto,texto,texto,texto,texto,numero";
    $joinQuery = "";
    
    echo CargaTablaPHP($tabla, $campos, $tiposcampo, $primaryKey, $joinQuery);
}

function CrearPlantilla_Handler() {
    $lmodo = isset($_POST['lmodo']) ? $_POST['lmodo'] : 'nuevo';
    $cod = isset($_POST['cod_plantilla']) ? $_POST['cod_plantilla'] : '';
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $tipo = isset($_POST['tipo_documento']) ? $_POST['tipo_documento'] : '';
    $contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
    $sql = isset($_POST['sql_consulta']) ? $_POST['sql_consulta'] : '';
    
    $resultado = CrearPlantilla($cod, $nombre, $descripcion, $tipo, $contenido, $sql, 1);
    echo $resultado;
}

function ActualizarPlantilla_Handler() {
    $cod = isset($_POST['cod_plantilla']) ? $_POST['cod_plantilla'] : '';
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $tipo = isset($_POST['tipo_documento']) ? $_POST['tipo_documento'] : '';
    $contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
    $sql = isset($_POST['sql_consulta']) ? $_POST['sql_consulta'] : '';
    $estado = isset($_POST['estado']) ? $_POST['estado'] : 1;
    
    $resultado = ActualizarPlantilla($cod, $nombre, $descripcion, $tipo, $contenido, $sql, $estado);
    echo $resultado;
}

function EliminarPlantilla_Handler() {
    $cod = isset($_POST['cod_plantilla']) ? $_POST['cod_plantilla'] : '';
    
    if (empty($cod)) {
        echo json_encode(['validacion' => 'ko', 'error' => 'Código requerido']);
        return;
    }
    
    $resultado = EliminarPlantilla($cod);
    echo $resultado;
}

function ReemplazarVariables_Handler() {
    $contenido = isset($_POST['contenido']) ? $_POST['contenido'] : '';
    $datos = isset($_POST['datos']) ? json_decode($_POST['datos'], true) : [];
    
    $contenido_final = ReemplazarVariables($contenido, $datos);
    
    echo json_encode([
        'validacion' => 'ok',
        'data' => [
            'contenido' => $contenido_final
        ]
    ]);
}

function GuardarDocumento_Handler() {
    $cod_plantilla = isset($_POST['cod_plantilla']) ? $_POST['cod_plantilla'] : '';
    $id_usuario = isset($_SESSION['user_codigo']) ? $_SESSION['user_codigo'] : null;
    $contenido_final = isset($_POST['contenido_final']) ? $_POST['contenido_final'] : '';
    $datos_json = isset($_POST['datos']) ? json_decode($_POST['datos'], true) : [];
    
    if (empty($contenido_final)) {
        echo json_encode(['validacion' => 'ko', 'error' => 'El contenido no puede estar vacío']);
        return;
    }
    
    $resultado = GuardarDocumento($cod_plantilla, $id_usuario, $contenido_final, $datos_json);
    echo $resultado;
}

?>
