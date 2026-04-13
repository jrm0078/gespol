<?php

header('Content-Type: application/json; charset=utf-8');

include '../config.inc.php';
include '../genericasPHP.php';
include '../func_datosPHP.php';
include 'func_plantillas.php';
include("../seguridad.php");

// ==========================================
// ENRUTADOR PRINCIPAL
// ==========================================

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch($action) {
    // GET
    case 'listar':
        listarPlantillas();
        break;
    case 'obtener_completa':
        obtenerCompleta();
        break;
    case 'obtener_filtros':
        obtenerFiltrosAction();
        break;
    
    // POST
    case 'crear':
        crearPlantilla();
        break;
    case 'editar':
        editarPlantilla();
        break;
    case 'eliminar':
        eliminarPlantillaAction();
        break;
    
    default:
        echo json_encode(['success' => false, 'error' => 'Acción no reconocida']);
}

// ==========================================
// FUNCIONES GET
// ==========================================

/**
 * Listar todas las plantillas
 */
function listarPlantillas() {
    $plantillas = ObtenerPlantillasAdmin();
    
    if ($plantillas) {
        $data = json_decode($plantillas, true);
        $formatted = [];
        
        foreach ($data as $plant) {
            $formatted[] = [
                'cod_plantilla' => $plant[0] ?? '',
                'nombre' => $plant[1] ?? '',
                'descripcion' => $plant[2] ?? '',
                'tipo_documento' => $plant[3] ?? '',
                'estado' => $plant[4] ?? 1
            ];
        }
        
        echo json_encode(['success' => true, 'data' => $formatted]);
    } else {
        echo json_encode(['success' => true, 'data' => []]);
    }
}

/**
 * Obtener plantilla completa con filtros
 */
function obtenerCompleta() {
    $cod = isset($_GET['cod']) ? $_GET['cod'] : '';
    
    if (empty($cod)) {
        echo json_encode(['success' => false, 'error' => 'Código requerido']);
        return;
    }
    
    $plantilla = ObtenerPlantilla($cod);
    
    if (!$plantilla) {
        echo json_encode(['success' => false, 'error' => 'Plantilla no encontrada']);
        return;
    }
    
    // Obtener filtros
    $filtros_json = ObtenerFiltros($cod);
    $filtros = json_decode($filtros_json, true) ?? [];
    
    // Formatear filtros
    $filtered = [];
    if (is_array($filtros)) {
        foreach ($filtros as $f) {
            $filtered[] = [
                'id' => $f[0] ?? '',
                'nombre_filtro' => $f[1] ?? '',
                'etiqueta' => $f[2] ?? '',
                'tipo_filtro' => $f[3] ?? '',
                'tabla_datos' => $f[4] ?? '',
                'campo_clave' => $f[5] ?? '',
                'campo_valor' => $f[6] ?? '',
                'sql_query' => $f[7] ?? '',
                'operador' => $f[8] ?? '',
                'requerido' => $f[9] ?? 0,
                'orden' => $f[10] ?? 999
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'cod_plantilla' => $plantilla['cod_plantilla'] ?? '',
            'nombre' => $plantilla['nombre'] ?? '',
            'descripcion' => $plantilla['descripcion'] ?? '',
            'tipo_documento' => $plantilla['tipo_documento'] ?? '',
            'contenido' => $plantilla['contenido'] ?? '',
            'sql_consulta' => $plantilla['sql_consulta'] ?? '',
            'estado' => $plantilla['estado'] ?? 1,
            'filtros' => $filtered
        ]
    ]);
}

/**
 * Obtener filtros de una plantilla
 */
function obtenerFiltrosAction() {
    $cod = isset($_GET['cod']) ? $_GET['cod'] : '';
    
    if (empty($cod)) {
        echo json_encode(['success' => false, 'error' => 'Código requerido']);
        return;
    }
    
    $filtros_json = ObtenerFiltros($cod);
    $filtros = json_decode($filtros_json, true) ?? [];
    
    echo json_encode(['success' => true, 'data' => $filtros]);
}

// ==========================================
// FUNCIONES POST
// ==========================================

/**
 * Crear nueva plantilla
 */
function crearPlantilla() {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'JSON inválido']);
        return;
    }
    
    $cod_plantilla = $data['cod_plantilla'] ?? '';
    $nombre = $data['nombre'] ?? '';
    $descripcion = $data['descripcion'] ?? '';
    $tipo_documento = $data['tipo_documento'] ?? '';
    $contenido = $data['contenido'] ?? '';
    $sql_consulta = $data['sql_consulta'] ?? '';
    $estado = $data['estado'] ?? 1;
    $filtros = $data['filtros'] ?? [];
    
    // Crear plantilla
    $resultado = CrearPlantilla($cod_plantilla, $nombre, $descripcion, $tipo_documento, 
                               $contenido, $sql_consulta, $estado);
    
    $res = json_decode($resultado, true);
    
    if ($res['validacion'] === 'ok') {
        // Guardar filtros
        if (!empty($filtros) && is_array($filtros)) {
            foreach ($filtros as $filtro) {
                AgregarFiltro(
                    $cod_plantilla,
                    $filtro['nombre_filtro'] ?? '',
                    $filtro['etiqueta'] ?? '',
                    $filtro['tipo_filtro'] ?? '',
                    $filtro['tabla_datos'] ?? '',
                    $filtro['campo_clave'] ?? '',
                    $filtro['campo_valor'] ?? '',
                    $filtro['sql_query'] ?? '',
                    $filtro['orden'] ?? 1,
                    $filtro['requerido'] ?? 0
                );
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Plantilla creada']);
    } else {
        echo json_encode(['success' => false, 'error' => $res['error'] ?? 'Error al crear']);
    }
}

/**
 * Actualizar plantilla existente
 */
function editarPlantilla() {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'JSON inválido']);
        return;
    }
    
    $cod_plantilla = $data['cod_plantilla'] ?? '';
    $nombre = $data['nombre'] ?? '';
    $descripcion = $data['descripcion'] ?? '';
    $tipo_documento = $data['tipo_documento'] ?? '';
    $contenido = $data['contenido'] ?? '';
    $sql_consulta = $data['sql_consulta'] ?? '';
    $estado = $data['estado'] ?? 1;
    $filtros = $data['filtros'] ?? [];
    
    // Actualizar plantilla
    $resultado = ActualizarPlantilla($cod_plantilla, $nombre, $descripcion, $tipo_documento, 
                                    $contenido, $sql_consulta, $estado);
    
    $res = json_decode($resultado, true);
    
    if ($res['validacion'] === 'ok') {
        // Eliminar filtros antiguos y crear nuevos
        EliminarFiltrosPorPlantilla($cod_plantilla);
        
        if (!empty($filtros) && is_array($filtros)) {
            foreach ($filtros as $filtro) {
                AgregarFiltro(
                    $cod_plantilla,
                    $filtro['nombre_filtro'] ?? '',
                    $filtro['etiqueta'] ?? '',
                    $filtro['tipo_filtro'] ?? '',
                    $filtro['tabla_datos'] ?? '',
                    $filtro['campo_clave'] ?? '',
                    $filtro['campo_valor'] ?? '',
                    $filtro['sql_query'] ?? '',
                    $filtro['orden'] ?? 1,
                    $filtro['requerido'] ?? 0
                );
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Plantilla actualizada']);
    } else {
        echo json_encode(['success' => false, 'error' => $res['error'] ?? 'Error al actualizar']);
    }
}

/**
 * Eliminar plantilla
 */
function eliminarPlantillaAction() {
    $cod_plantilla = isset($_GET['cod_plantilla']) ? $_GET['cod_plantilla'] : '';
    
    if (empty($cod_plantilla)) {
        echo json_encode(['success' => false, 'error' => 'Código requerido']);
        return;
    }
    
    $resultado = EliminarPlantilla($cod_plantilla);
    $res = json_decode($resultado, true);
    
    if ($res['validacion'] === 'ok') {
        echo json_encode(['success' => true, 'message' => 'Plantilla eliminada']);
    } else {
        echo json_encode(['success' => false, 'error' => $res['error'] ?? 'Error al eliminar']);
    }
}

?>

    
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
