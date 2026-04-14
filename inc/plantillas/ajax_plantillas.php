<?php
ob_start(); // captura cualquier output accidental de los includes

include '../config.inc.php';
include '../genericasPHP.php';
include '../func_datosPHP.php';
include 'func_plantillas.php';
include("../seguridad.php");

ob_end_clean(); // descarta el output acumulado
header('Content-Type: application/json; charset=utf-8');

// ==========================================
// ENRUTADOR PRINCIPAL
// ==========================================

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch($action) {
    case 'listar':
        listarPlantillas(false);
        break;
    case 'listar_activas':
        listarPlantillas(true);
        break;
    case 'obtener_completa':
        obtenerCompleta();
        break;
    case 'obtener_filtros':
        obtenerFiltrosAction();
        break;
    case 'obtener_datos_filtrados':
        obtenerDatosFiltrados();
        break;
    case 'ejecutar_select_filtro':
        ejecutarSelectFiltro();
        break;
    case 'crear':
        crearPlantilla();
        break;
    case 'editar':
        editarPlantilla();
        break;
    case 'eliminar':
        eliminarPlantillaAction();
        break;
    case 'guardar_documento':
        guardarDocumentoAction();
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Acción no reconocida']);
}

// ==========================================
// FUNCIONES GET
// ==========================================

/**
 * Listar plantillas. $soloActivas=true filtra estado=1
 */
function listarPlantillas($soloActivas = false) {
    $plantillas_json = $soloActivas ? ObtenerPlantillas() : ObtenerPlantillasAdmin();
    $data = json_decode($plantillas_json, true) ?? [];
    
    $formatted = [];
    foreach ($data as $plant) {
        $formatted[] = [
            'cod_plantilla' => $plant['cod_plantilla'] ?? '',
            'nombre'        => $plant['nombre']        ?? '',
            'descripcion'   => $plant['descripcion']   ?? '',
            'tipo_documento'=> $plant['tipo_documento']?? '',
            'estado'        => $plant['estado']        ?? 1
        ];
    }
    echo json_encode(['success' => true, 'data' => $formatted]);
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
                'id'           => $f['id']           ?? '',
                'nombre_filtro'=> $f['nombre_filtro'] ?? '',
                'etiqueta'     => $f['etiqueta']      ?? '',
                'tipo_filtro'  => $f['tipo_filtro']   ?? '',
                'tabla_datos'  => $f['tabla_datos']   ?? '',
                'campo_clave'  => $f['campo_clave']   ?? '',
                'campo_valor'  => $f['campo_valor']   ?? '',
                'sql_query'    => $f['sql_query']     ?? '',
                'operador'     => $f['operador']      ?? '',
                'requerido'    => $f['requerido']     ?? 0,
                'orden'        => $f['orden']         ?? 999
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
 * Obtener filtros de una plantilla con valores cargados para selects
 */
function obtenerFiltrosAction() {
    $cod = isset($_GET['cod']) ? $_GET['cod'] : '';
    
    if (empty($cod)) {
        echo json_encode(['success' => false, 'error' => 'Código requerido']);
        return;
    }
    
    $filtros_json = ObtenerFiltros($cod);
    $filtros = json_decode($filtros_json, true) ?? [];
    
    // Enriquecer cada filtro con sus valores posibles
    foreach ($filtros as &$filtro) {
        $tipo = $filtro['tipo_filtro'] ?? 'select_table';
        $filtro['valores'] = [];
        $filtro['tiene_parametros'] = false;
        $filtro['parametros_requeridos'] = [];
        
        if ($tipo === 'select_table') {
            try {
                $tabla       = preg_replace('/[^a-zA-Z0-9_]/', '', $filtro['tabla_datos'] ?? '');
                $campo_clave = preg_replace('/[^a-zA-Z0-9_]/', '', $filtro['campo_clave'] ?? 'id');
                $campo_valor = preg_replace('/[^a-zA-Z0-9_]/', '', $filtro['campo_valor'] ?? 'nombre');
                if ($tabla) {
                    try {
                        $sql = "SELECT `{$campo_clave}` as id, `{$campo_valor}` as valor FROM `{$tabla}` WHERE activo = 1 ORDER BY `{$campo_valor}`";
                        $rows = selectPHP($sql);
                    } catch (Exception $e1) {
                        $sql = "SELECT `{$campo_clave}` as id, `{$campo_valor}` as valor FROM `{$tabla}` ORDER BY `{$campo_valor}`";
                        $rows = selectPHP($sql);
                    }
                    if (is_array($rows)) {
                        $filtro['valores'] = $rows;
                    }
                }
            } catch (Exception $e) {
                // silencio
            }
        } elseif ($tipo === 'select_sql') {
            $sql = $filtro['sql_query'] ?? '';
            if (!empty($sql)) {
                $has_params = preg_match_all('/\[\[(\w+)\]\]/', $sql, $matches);
                if ($has_params > 0) {
                    $filtro['tiene_parametros'] = true;
                    $filtro['parametros_requeridos'] = $matches[1];
                } else {
                    try {
                        $rows = selectPHP($sql);
                        if (is_array($rows)) {
                            foreach ($rows as $row) {
                                $keys = array_values($row);
                                $filtro['valores'][] = ['id' => $keys[0], 'valor' => $keys[1] ?? $keys[0]];
                            }
                        }
                    } catch (Exception $e) {
                        // silencio
                    }
                }
            }
        }
    }
    unset($filtro);
    
    echo json_encode(['success' => true, 'data' => $filtros]);
}

/**
 * Ejecutar SELECT de un filtro con parámetros nombrados [[param]]
 */
function ejecutarSelectFiltro() {
    $cod           = isset($_GET['cod'])    ? $_GET['cod']    : '';
    $nombre_filtro = isset($_GET['filtro']) ? $_GET['filtro'] : '';
    $params_json   = isset($_GET['parametros']) ? $_GET['parametros'] : '{}';
    
    if (!$cod || !$nombre_filtro) {
        echo json_encode(['success' => false, 'error' => 'Parámetros requeridos']);
        return;
    }
    
    $params = json_decode($params_json, true) ?? [];
    
    $filtros_json = ObtenerFiltros($cod);
    $filtros = json_decode($filtros_json, true) ?? [];
    
    $filtro_config = null;
    foreach ($filtros as $f) {
        if ($f['nombre_filtro'] === $nombre_filtro) {
            $filtro_config = $f;
            break;
        }
    }
    
    if (!$filtro_config) {
        echo json_encode(['success' => false, 'error' => 'Filtro no encontrado']);
        return;
    }
    
    $sql = $filtro_config['sql_query'] ?? '';
    if (empty($sql)) {
        echo json_encode(['success' => false, 'error' => 'SQL vacío']);
        return;
    }
    
    try {
        $has_params = preg_match_all('/\[\[(\w+)\]\]/', $sql, $matches);
        $param_values = [];
        if ($has_params > 0) {
            foreach ($matches[1] as $pname) {
                $param_values[] = $params[$pname] ?? null;
            }
            $sql = preg_replace('/\[\[\w+\]\]/', '?', $sql);
        }
        
        $db   = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($param_values);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db   = null;
        
        $valores = [];
        foreach ($rows as $row) {
            $keys = array_values($row);
            $valores[] = ['id' => $keys[0], 'valor' => $keys[1] ?? $keys[0]];
        }
        echo json_encode(['success' => true, 'data' => $valores]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

/**
 * Obtener datos con filtros aplicados (ejecuta el SQL de la plantilla)
 */
function obtenerDatosFiltrados() {
    $cod         = isset($_GET['cod'])     ? $_GET['cod']     : '';
    $filtros_raw = isset($_GET['filtros']) ? $_GET['filtros'] : '{}';
    
    if (empty($cod)) {
        echo json_encode(['success' => false, 'error' => 'Código de plantilla requerido']);
        return;
    }
    
    $filtros_vals = json_decode($filtros_raw, true) ?? [];
    
    $plantilla = ObtenerPlantilla($cod);
    if (!$plantilla) {
        echo json_encode(['success' => false, 'error' => 'Plantilla no encontrada']);
        return;
    }
    
    $sql_consulta = $plantilla['sql_consulta'] ?? '';
    if (empty($sql_consulta)) {
        echo json_encode(['success' => false, 'error' => 'La plantilla no tiene SQL configurado']);
        return;
    }
    
    try {
        $db = getConnection();
        
        // Detectar parámetros nombrados [[param_name]]
        $has_named = preg_match_all('/\[\[(\w+)\]\]/', $sql_consulta, $named_matches);
        $param_values = [];
        
        if ($has_named > 0) {
            foreach ($named_matches[1] as $pname) {
                $param_values[] = $filtros_vals[$pname] ?? null;
            }
            $sql_consulta = preg_replace('/\[\[\w+\]\]/', '?', $sql_consulta);
        } else {
            // Sin parámetros nombrados: recopilar valores en orden de filtros configurados
            $filtros_json = ObtenerFiltros($cod);
            $filtros_cfg  = json_decode($filtros_json, true) ?? [];
            foreach ($filtros_cfg as $fc) {
                $val = $filtros_vals[$fc['nombre_filtro']] ?? null;
                if ($val !== null && $val !== '') {
                    $param_values[] = $val;
                }
            }
        }
        
        $stmt = $db->prepare($sql_consulta);
        $stmt->execute($param_values);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db = null;
        
        if (empty($datos)) {
            echo json_encode(['success' => false, 'error' => 'No se encontraron datos con los filtros aplicados']);
            return;
        }
        
        $respuesta = (count($datos) === 1) ? $datos[0] : $datos;
        echo json_encode(['success' => true, 'data' => $respuesta]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

/**
 * Guardar documento generado en BD
 */
function guardarDocumentoAction() {
    $input = file_get_contents('php://input');
    $data  = json_decode($input, true);
    
    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'JSON inválido']);
        return;
    }
    
    $cod_plantilla  = $data['cod_plantilla']  ?? '';
    $contenido_final= $data['contenido_final']?? '';
    $datos_json     = json_encode($data['datos'] ?? []);
    
    if (empty($cod_plantilla) || empty(trim($contenido_final)) || $contenido_final === '<p></p>') {
        echo json_encode(['success' => false, 'error' => 'Código y contenido son requeridos']);
        return;
    }
    
    // Obtener id_usuario de sesión si existe
    $id_usuario = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 'NULL';
    
    $cod_sql     = CadSql($cod_plantilla);
    $cont_sql    = CadSql($contenido_final);
    $datos_sql   = CadSql($datos_json);
    $usuario_val = ($id_usuario === 'NULL') ? 'NULL' : $id_usuario;
    
    $query = "INSERT INTO plantillas_documentos (cod_plantilla, id_usuario, contenido_final, datos_json)
              VALUES ('$cod_sql', $usuario_val, '$cont_sql', '$datos_sql')";
    
    $result = ejecutaqueryPHP($query);
    if ($result === 'OK') {
        echo json_encode(['success' => true, 'message' => 'Documento guardado correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al guardar el documento']);
    }
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

