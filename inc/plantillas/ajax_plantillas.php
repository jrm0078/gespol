<?php
ob_start(); // captura cualquier output accidental de los includes

include_once '../config.inc.php';
include_once '../genericasPHP.php';
include_once '../func_datosPHP.php';
include_once 'func_plantillas.php';
include_once "../seguridad.php";

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
        actionCrear();
        break;
    case 'editar':
        actionEditar();
        break;
    case 'eliminar':
        eliminarPlantillaAction();
        break;
    case 'guardar_ayuda':
        guardarAyudaAction();
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
            'ayuda' => $plantilla['ayuda'] ?? '',
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
 * Crear nueva plantilla (POST JSON)
 */
function actionCrear() {
    $input = file_get_contents('php://input');
    $data  = json_decode($input, true);

    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'JSON inválido']);
        return;
    }

    $cod         = $data['cod_plantilla']  ?? '';
    $nombre      = $data['nombre']         ?? '';
    $descripcion = $data['descripcion']    ?? '';
    $tipo        = $data['tipo_documento'] ?? '';
    $contenido   = $data['contenido']      ?? '';
    $sql         = $data['sql_consulta']   ?? '';
    $ayuda       = $data['ayuda']          ?? '';
    $estado      = isset($data['estado'])  ? (int)$data['estado'] : 1;
    $filtros     = $data['filtros']        ?? [];

    $result = json_decode(CrearPlantilla($cod, $nombre, $descripcion, $tipo, $contenido, $sql, $estado, $ayuda), true);

    if (($result['validacion'] ?? '') !== 'ok') {
        echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Error al crear']);
        return;
    }

    foreach ($filtros as $f) {
        AgregarFiltro(
            $cod,
            $f['nombre_filtro'] ?? '',
            $f['etiqueta']      ?? '',
            $f['tipo_filtro']   ?? 'select_table',
            $f['tabla_datos']   ?? '',
            $f['campo_clave']   ?? 'id',
            $f['campo_valor']   ?? 'nombre',
            $f['sql_query']     ?? '',
            $f['orden']         ?? 1,
            isset($f['requerido']) ? (int)$f['requerido'] : 0
        );
    }

    echo json_encode(['success' => true, 'message' => 'Plantilla creada correctamente']);
}

/**
 * Editar plantilla existente (POST JSON, cod en GET)
 */
function actionEditar() {
    $cod   = isset($_GET['cod']) ? $_GET['cod'] : '';
    $input = file_get_contents('php://input');
    $data  = json_decode($input, true);

    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'JSON inválido']);
        return;
    }

    if (empty($cod)) {
        $cod = $data['cod_plantilla'] ?? '';
    }

    $nombre      = $data['nombre']         ?? '';
    $descripcion = $data['descripcion']    ?? '';
    $tipo        = $data['tipo_documento'] ?? '';
    $contenido   = $data['contenido']      ?? '';
    $sql         = $data['sql_consulta']   ?? '';
    $ayuda       = $data['ayuda']          ?? '';
    $estado      = isset($data['estado'])  ? (int)$data['estado'] : 1;
    $filtros     = $data['filtros']        ?? [];

    $result = json_decode(ActualizarPlantilla($cod, $nombre, $descripcion, $tipo, $contenido, $sql, $estado, $ayuda), true);

    if (($result['validacion'] ?? '') !== 'ok') {
        echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Error al actualizar']);
        return;
    }

    // Reemplazar filtros
    EliminarFiltrosPorPlantilla($cod);
    foreach ($filtros as $f) {
        AgregarFiltro(
            $cod,
            $f['nombre_filtro'] ?? '',
            $f['etiqueta']      ?? '',
            $f['tipo_filtro']   ?? 'select_table',
            $f['tabla_datos']   ?? '',
            $f['campo_clave']   ?? 'id',
            $f['campo_valor']   ?? 'nombre',
            $f['sql_query']     ?? '',
            $f['orden']         ?? 1,
            isset($f['requerido']) ? (int)$f['requerido'] : 0
        );
    }

    echo json_encode(['success' => true, 'message' => 'Plantilla actualizada correctamente']);
}

/**
 * Eliminar plantilla y sus filtros
 */
function eliminarPlantillaAction() {
    $cod = isset($_GET['cod_plantilla']) ? $_GET['cod_plantilla'] : '';
    if (empty($cod)) {
        echo json_encode(['success' => false, 'error' => 'Código requerido']);
        return;
    }

    EliminarFiltrosPorPlantilla($cod);
    $result = json_decode(EliminarPlantilla($cod), true);

    if (($result['validacion'] ?? '') === 'ok') {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Error al eliminar']);
    }
}

/**
 * Guardar solo el campo ayuda de una plantilla
 */
function guardarAyudaAction() {
    $cod   = isset($_GET['cod']) ? $_GET['cod'] : '';
    $input = file_get_contents('php://input');
    $data  = json_decode($input, true);

    if (empty($cod) || !$data) {
        echo json_encode(['success' => false, 'error' => 'Parámetros requeridos']);
        return;
    }

    $cod_sql   = CadSql($cod);
    $ayuda_sql = CadSql($data['ayuda'] ?? '');

    $query = "UPDATE plantillas_maestro SET ayuda = '$ayuda_sql' WHERE cod_plantilla = '$cod_sql'";
    $result = ejecutaqueryPHP($query);

    if ($result === 'OK') {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al guardar']);
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

?>

