<?php

include '../config.inc.php';
include '../genericasPHP.php';
include '../func_datosPHP.php';

/**
 * MÓDULO PLANTILLAS - Funciones CRUD
 * Manejo de plantillas, filtros y generación de documentos
 */

// ============================================
// FUNCIONES PLANTILLAS MAESTRO
// ============================================

function ObtenerPlantillas() {
    $query = "SELECT cod_plantilla, nombre, descripcion, tipo_documento, estado, fecha_creacion 
              FROM plantillas_maestro 
              WHERE estado = 1
              ORDER BY nombre";
    return select($query);
}

function ObtenerPlantillasAdmin() {
    $query = "SELECT cod_plantilla, nombre, descripcion, tipo_documento, estado, fecha_creacion 
              FROM plantillas_maestro 
              ORDER BY nombre DESC";
    return select($query);
}

function ObtenerPlantilla($cod_plantilla) {
    $cod = CadSql($cod_plantilla);
    $query = "SELECT cod_plantilla, nombre, descripcion, tipo_documento, contenido, 
                     tabla_origen, campo_clave, sql_consulta, estado
              FROM plantillas_maestro 
              WHERE cod_plantilla = '$cod'";
    $result = select($query);
    if ($result) {
        $data = json_decode($result, true);
        return isset($data[0]) ? $data[0] : null;
    }
    return null;
}

function CrearPlantilla($cod_plantilla, $nombre, $descripcion, $tipo_documento, 
                        $contenido, $sql_consulta, $estado = 1) {
    
    // Validaciones
    $mensajeError = "";
    
    if (empty($cod_plantilla)) {
        $mensajeError .= "Código de plantilla requerido<br>";
    }
    if (empty($nombre)) {
        $mensajeError .= "Nombre de plantilla requerido<br>";
    }
    if (strlen($nombre) < 3) {
        $mensajeError .= "El nombre debe tener mínimo 3 caracteres<br>";
    }
    if (empty($contenido)) {
        $mensajeError .= "El contenido HTML es requerido<br>";
    }
    if (strpos($cod_plantilla, ' ') !== false) {
        $mensajeError .= "El código no puede contener espacios<br>";
    }
    
    if ($mensajeError) {
        return json_encode(['validacion' => 'warning', 'error' => $mensajeError]);
    }
    
    // Insertar plantilla
    $cod_sql = CadSql($cod_plantilla);
    $nom_sql = CadSql($nombre);
    $desc_sql = CadSql($descripcion);
    $tipo_sql = CadSql($tipo_documento);
    $cont_sql = CadSql($contenido);
    $sql_sql = CadSql($sql_consulta);
    
    $query = "INSERT INTO plantillas_maestro 
              (cod_plantilla, nombre, descripcion, tipo_documento, contenido, sql_consulta, estado)
              VALUES ('$cod_sql', '$nom_sql', '$desc_sql', '$tipo_sql', '$cont_sql', '$sql_sql', $estado)";
    
    if (insert($query)) {
        return json_encode(['validacion' => 'ok', 'mensaje' => 'Plantilla creada correctamente']);
    } else {
        return json_encode(['validacion' => 'ko', 'error' => 'Error al crear la plantilla']);
    }
}

function ActualizarPlantilla($cod_plantilla, $nombre, $descripcion, $tipo_documento, 
                             $contenido, $sql_consulta, $estado = 1) {
    
    // Validaciones
    $mensajeError = "";
    
    if (empty($cod_plantilla)) {
        $mensajeError .= "Código de plantilla requerido<br>";
    }
    if (empty($nombre)) {
        $mensajeError .= "Nombre de plantilla requerido<br>";
    }
    if (strlen($nombre) < 3) {
        $mensajeError .= "El nombre debe tener mínimo 3 caracteres<br>";
    }
    if (empty($contenido)) {
        $mensajeError .= "El contenido HTML es requerido<br>";
    }
    
    if ($mensajeError) {
        return json_encode(['validacion' => 'warning', 'error' => $mensajeError]);
    }
    
    $cod_sql = CadSql($cod_plantilla);
    $nom_sql = CadSql($nombre);
    $desc_sql = CadSql($descripcion);
    $tipo_sql = CadSql($tipo_documento);
    $cont_sql = CadSql($contenido);
    $sql_sql = CadSql($sql_consulta);
    
    $query = "UPDATE plantillas_maestro 
              SET nombre = '$nom_sql', 
                  descripcion = '$desc_sql',
                  tipo_documento = '$tipo_sql',
                  contenido = '$cont_sql',
                  sql_consulta = '$sql_sql',
                  estado = $estado
              WHERE cod_plantilla = '$cod_sql'";
    
    if (update($query)) {
        return json_encode(['validacion' => 'ok', 'mensaje' => 'Plantilla actualizada correctamente']);
    } else {
        return json_encode(['validacion' => 'ko', 'error' => 'Error al actualizar la plantilla']);
    }
}

function EliminarPlantilla($cod_plantilla) {
    $cod_sql = CadSql($cod_plantilla);
    
    $query = "DELETE FROM plantillas_maestro WHERE cod_plantilla = '$cod_sql'";
    
    if (delete($query)) {
        return json_encode(['validacion' => 'ok', 'mensaje' => 'Plantilla eliminada correctamente']);
    } else {
        return json_encode(['validacion' => 'ko', 'error' => 'Error al eliminar la plantilla']);
    }
}

// ============================================
// FUNCIONES FILTROS
// ============================================

function ObtenerFiltros($cod_plantilla) {
    $cod_sql = CadSql($cod_plantilla);
    $query = "SELECT id, nombre_filtro, etiqueta, tipo_filtro, tabla_datos, 
                     campo_clave, campo_valor, sql_query, operador, requerido, orden
              FROM plantillas_filtros 
              WHERE cod_plantilla = '$cod_sql' AND activo = 1
              ORDER BY orden ASC";
    return select($query);
}

function AgregarFiltro($cod_plantilla, $nombre_filtro, $etiqueta, $tipo_filtro, 
                       $tabla_datos, $campo_clave, $campo_valor, $sql_query, $orden, $requerido) {
    
    $cod_sql = CadSql($cod_plantilla);
    $nom_sql = CadSql($nombre_filtro);
    $eti_sql = CadSql($etiqueta);
    $tipo_sql = CadSql($tipo_filtro);
    $tab_sql = CadSql($tabla_datos);
    $camp_sql = CadSql($campo_clave);
    $camp2_sql = CadSql($campo_valor);
    $sql_sql = CadSql($sql_query);
    
    $query = "INSERT INTO plantillas_filtros 
              (cod_plantilla, nombre_filtro, etiqueta, tipo_filtro, tabla_datos, 
               campo_clave, campo_valor, sql_query, orden, requerido, activo)
              VALUES ('$cod_sql', '$nom_sql', '$eti_sql', '$tipo_sql', '$tab_sql', 
                      '$camp_sql', '$camp2_sql', '$sql_sql', $orden, $requerido, 1)";
    
    return insert($query);
}

function EliminarFiltrosPorPlantilla($cod_plantilla) {
    $cod_sql = CadSql($cod_plantilla);
    $query = "DELETE FROM plantillas_filtros WHERE cod_plantilla = '$cod_sql'";
    return delete($query);
}

// ============================================
// FUNCIONES VARIABLES
// ============================================

function ObtenerVariables($cod_plantilla) {
    $cod_sql = CadSql($cod_plantilla);
    $query = "SELECT id, nombre_variable, etiqueta, tipo, requerido, orden
              FROM plantillas_variables 
              WHERE cod_plantilla = '$cod_sql' AND activo = 1
              ORDER BY orden ASC";
    return select($query);
}

// ============================================
// FUNCIONES GENERACIÓN DE DOCUMENTOS
// ============================================

function ReemplazarVariables($contenido, $datos) {
    $contenido_final = $contenido;
    
    if ($datos && is_array($datos)) {
        foreach ($datos as $variable => $valor) {
            $patron = '{%%' . $variable . '%%}';
            $contenido_final = str_replace($patron, $valor, $contenido_final);
        }
    }
    
    return $contenido_final;
}

function GuardarDocumento($cod_plantilla, $id_usuario, $contenido_final, $datos_json) {
    
    $cod_sql = CadSql($cod_plantilla);
    $cont_sql = CadSql($contenido_final);
    $datos_sql = CadSql(json_encode($datos_json));
    
    $query = "INSERT INTO plantillas_documentos 
              (cod_plantilla, id_usuario, contenido_final, datos_json)
              VALUES ('$cod_sql', $id_usuario, '$cont_sql', '$datos_sql')";
    
    if (insert($query)) {
        return json_encode(['validacion' => 'ok', 'mensaje' => 'Documento guardado correctamente']);
    } else {
        return json_encode(['validacion' => 'ko', 'error' => 'Error al guardar el documento']);
    }
}

function ObtenerDocumentos($cod_plantilla) {
    $cod_sql = CadSql($cod_plantilla);
    $query = "SELECT id, cod_plantilla, contenido_final, datos_json, fecha_creacion
              FROM plantillas_documentos 
              WHERE cod_plantilla = '$cod_sql'
              ORDER BY fecha_creacion DESC";
    return select($query);
}

?>
