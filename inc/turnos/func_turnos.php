<?php
// ============================================================
// MÓDULO TURNOS – Funciones de datos
// ============================================================

// ── EJERCICIOS ──────────────────────────────────────────────

function turnos_listarEjercicios() {
    return selectPHP("SELECT id, ejercicio, descripcion, total_horas, estado FROM turnos_ejercicio ORDER BY ejercicio DESC");
}

function turnos_guardarEjercicio($d) {
    $id          = intval($d['id'] ?? 0);
    $ejercicio   = intval($d['ejercicio'] ?? 0);
    $descripcion = CadSql($d['descripcion'] ?? '');
    $total_horas = floatval($d['total_horas'] ?? 1498);
    $estado      = in_array($d['estado'] ?? '', ['abierto','cerrado']) ? $d['estado'] : 'abierto';

    if (!$ejercicio) return '{"validacion":"warning","mensaje":"Indicar año del ejercicio"}';
    try {
        $db = getConnection();
        if ($id > 0) {
            $s = $db->prepare("UPDATE turnos_ejercicio SET ejercicio=?,descripcion=?,total_horas=?,estado=? WHERE id=?");
            $s->execute([$ejercicio,$descripcion,$total_horas,$estado,$id]);
        } else {
            $ex = $db->prepare("SELECT COUNT(*) FROM turnos_ejercicio WHERE ejercicio=?");
            $ex->execute([$ejercicio]);
            if ($ex->fetchColumn() > 0) return '{"validacion":"warning","mensaje":"Ya existe ese ejercicio"}';
            $s = $db->prepare("INSERT INTO turnos_ejercicio (ejercicio,descripcion,total_horas,estado) VALUES (?,?,?,?)");
            $s->execute([$ejercicio,$descripcion,$total_horas,$estado]);
            $id = $db->lastInsertId();
        }
        $db = null;
        return json_encode(['validacion'=>'ok','id'=>$id,'mensaje'=>'Ejercicio guardado']);
    } catch(PDOException $e) { return json_encode(['validacion'=>'error','error'=>$e->getMessage()]); }
}

// ── EQUIPOS ─────────────────────────────────────────────────

function turnos_listarEquipos($soloActivos = false) {
    $w = $soloActivos ? "WHERE e.activo=1" : "";
    return selectPHP("
        SELECT e.id, e.codigo, e.nombre, e.orden, e.activo,
               COUNT(tea.id) as num_agentes
        FROM turnos_equipo e
        LEFT JOIN turnos_equipo_agente tea ON tea.id_equipo=e.id AND tea.activo=1
        $w
        GROUP BY e.id ORDER BY e.orden, e.nombre");
}

function turnos_obtenerEquipo($id) {
    $id = intval($id);
    return selectPHP("SELECT * FROM turnos_equipo WHERE id=$id");
}

function turnos_guardarEquipo($d) {
    $id     = intval($d['id'] ?? 0);
    $codigo = CadSql($d['codigo'] ?? '');
    $nombre = CadSql($d['nombre'] ?? '');
    $orden  = intval($d['orden'] ?? 0);
    $activo = intval($d['activo'] ?? 1);

    if ($codigo === '') return '{"validacion":"warning","mensaje":"Indicar código del equipo"}';
    if ($nombre === '') return '{"validacion":"warning","mensaje":"Indicar nombre del equipo"}';
    try {
        $db = getConnection();
        if ($id > 0) {
            $s = $db->prepare("UPDATE turnos_equipo SET codigo=?,nombre=?,orden=?,activo=? WHERE id=?");
            $s->execute([$codigo,$nombre,$orden,$activo,$id]);
        } else {
            $ex = $db->prepare("SELECT COUNT(*) FROM turnos_equipo WHERE codigo=?");
            $ex->execute([$codigo]);
            if ($ex->fetchColumn() > 0) return '{"validacion":"warning","mensaje":"Ya existe un equipo con ese código"}';
            $s = $db->prepare("INSERT INTO turnos_equipo (codigo,nombre,orden,activo) VALUES (?,?,?,?)");
            $s->execute([$codigo,$nombre,$orden,$activo]);
            $id = $db->lastInsertId();
        }
        $db = null;
        return json_encode(['validacion'=>'ok','id'=>$id,'mensaje'=>'Equipo guardado']);
    } catch(PDOException $e) { return json_encode(['validacion'=>'error','error'=>$e->getMessage()]); }
}

function turnos_eliminarEquipo($id) {
    $id = intval($id);
    try {
        $db = getConnection();
        $usado = $db->query("SELECT COUNT(*) FROM turnos_cuadrante_dia WHERE id_equipo=$id")->fetchColumn();
        if ($usado > 0) return '{"validacion":"warning","mensaje":"No se puede eliminar: el equipo tiene días registrados en cuadrante"}';
        $db->exec("DELETE FROM turnos_equipo_agente WHERE id_equipo=$id");
        $db->exec("DELETE FROM turnos_equipo WHERE id=$id");
        $db = null;
        return '{"validacion":"ok","mensaje":"Equipo eliminado"}';
    } catch(PDOException $e) { return json_encode(['validacion'=>'error','error'=>$e->getMessage()]); }
}

// ── EQUIPO–AGENTE ────────────────────────────────────────────

function turnos_listarAgentesEquipo($id_equipo) {
    $id_equipo = intval($id_equipo);
    return selectPHP("
        SELECT tea.id, tea.numagente, tea.orden, tea.activo,
               tea.fecha_desde, tea.fecha_hasta,
               CONCAT(a.nombre, IF(a.indicativo IS NOT NULL AND a.indicativo != '', CONCAT(' [',a.indicativo,']'),'')) as nombre_completo,
               a.nombre, a.indicativo
        FROM turnos_equipo_agente tea
        JOIN agentes a ON a.numagente = tea.numagente
        WHERE tea.id_equipo = $id_equipo
        ORDER BY tea.orden, a.nombre");
}

function turnos_asignarAgente($id_equipo, $numagente, $orden, $fecha_desde) {
    $id_equipo  = intval($id_equipo);
    $numagente  = intval($numagente);
    $orden      = intval($orden ?? 99);
    $fecha      = $fecha_desde ?: '2026-01-01';
    try {
        $db = getConnection();
        // Check if agent already in this team active
        $ex = $db->prepare("SELECT COUNT(*) FROM turnos_equipo_agente WHERE id_equipo=? AND numagente=? AND activo=1");
        $ex->execute([$id_equipo, $numagente]);
        if ($ex->fetchColumn() > 0) return '{"validacion":"warning","mensaje":"El agente ya está en este equipo"}';
        $s = $db->prepare("INSERT INTO turnos_equipo_agente (id_equipo,numagente,orden,fecha_desde,activo) VALUES (?,?,?,?,1)");
        $s->execute([$id_equipo,$numagente,$orden,$fecha]);
        $db = null;
        return '{"validacion":"ok","mensaje":"Agente asignado"}';
    } catch(PDOException $e) { return json_encode(['validacion'=>'error','error'=>$e->getMessage()]); }
}

function turnos_quitarAgente($id) {
    $id = intval($id);
    try {
        $db = getConnection();
        $db->exec("DELETE FROM turnos_equipo_agente WHERE id=$id");
        $db = null;
        return '{"validacion":"ok","mensaje":"Agente quitado del equipo"}';
    } catch(PDOException $e) { return json_encode(['validacion'=>'error','error'=>$e->getMessage()]); }
}

function turnos_comboAgentes($id_equipo = 0) {
    // Devuelve agentes activos NO asignados ya al equipo dado
    $id_equipo = intval($id_equipo);
    $excl = $id_equipo > 0
        ? "AND a.numagente NOT IN (SELECT numagente FROM turnos_equipo_agente WHERE id_equipo=$id_equipo AND activo=1)"
        : "";
    return selectPHP("
        SELECT a.numagente as id,
               CONCAT(a.numagente,' – ',IFNULL(a.indicativo,''),' – ',a.nombre) as texto
        FROM agentes a WHERE a.activo=1 $excl ORDER BY a.numagente");
}

// ── CÓDIGOS / NOMENCLATURAS ──────────────────────────────────

function turnos_listarCodigos() {
    return selectPHP("SELECT * FROM turnos_codigo ORDER BY orden, codigo");
}

function turnos_guardarCodigo($d) {
    $id      = intval($d['id'] ?? 0);
    $codigo  = CadSql($d['codigo'] ?? '');
    $desc    = CadSql($d['descripcion'] ?? '');
    $color   = CadSql($d['color'] ?? '#cccccc');
    $computa = intval($d['computa'] ?? 0);
    $tipo    = in_array($d['tipo_computo'] ?? '', ['normal','extra','reducida','ninguno']) ? $d['tipo_computo'] : 'ninguno';
    $afJor   = intval($d['afecta_jornada'] ?? 0);
    $afExt   = intval($d['afecta_extra'] ?? 0);
    $reqObs  = intval($d['requiere_observacion'] ?? 0);
    $activo  = intval($d['activo'] ?? 1);
    $orden   = intval($d['orden'] ?? 0);

    if ($codigo === '') return '{"validacion":"warning","mensaje":"Indicar código"}';
    if ($desc === '')   return '{"validacion":"warning","mensaje":"Indicar descripción"}';
    try {
        $db = getConnection();
        if ($id > 0) {
            $s = $db->prepare("UPDATE turnos_codigo SET codigo=?,descripcion=?,color=?,computa=?,tipo_computo=?,afecta_jornada=?,afecta_extra=?,requiere_observacion=?,activo=?,orden=? WHERE id=?");
            $s->execute([$codigo,$desc,$color,$computa,$tipo,$afJor,$afExt,$reqObs,$activo,$orden,$id]);
        } else {
            $ex = $db->prepare("SELECT COUNT(*) FROM turnos_codigo WHERE codigo=?");
            $ex->execute([$codigo]);
            if ($ex->fetchColumn() > 0) return '{"validacion":"warning","mensaje":"Ya existe un código igual"}';
            $s = $db->prepare("INSERT INTO turnos_codigo (codigo,descripcion,color,computa,tipo_computo,afecta_jornada,afecta_extra,requiere_observacion,activo,orden) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $s->execute([$codigo,$desc,$color,$computa,$tipo,$afJor,$afExt,$reqObs,$activo,$orden]);
            $id = $db->lastInsertId();
        }
        $db = null;
        return json_encode(['validacion'=>'ok','id'=>$id,'mensaje'=>'Código guardado']);
    } catch(PDOException $e) { return json_encode(['validacion'=>'error','error'=>$e->getMessage()]); }
}

function turnos_eliminarCodigo($id) {
    $id = intval($id);
    try {
        $db = getConnection();
        $cod = $db->query("SELECT codigo FROM turnos_codigo WHERE id=$id")->fetchColumn();
        if ($cod) {
            $usado = $db->prepare("SELECT COUNT(*) FROM turnos_cuadrante_dia WHERE codigo=?");
            $usado->execute([$cod]);
            if ($usado->fetchColumn() > 0) return '{"validacion":"warning","mensaje":"No se puede eliminar: el código está usado en cuadrante"}';
        }
        $db->exec("DELETE FROM turnos_codigo WHERE id=$id");
        $db = null;
        return '{"validacion":"ok","mensaje":"Código eliminado"}';
    } catch(PDOException $e) { return json_encode(['validacion'=>'error','error'=>$e->getMessage()]); }
}

// ── CALENDARIO – FESTIVOS ────────────────────────────────────

function turnos_listarFestivos($ejercicio) {
    $ejercicio = intval($ejercicio);
    return selectPHP("SELECT * FROM turnos_calendario_dia WHERE ejercicio=$ejercicio ORDER BY fecha");
}

function turnos_guardarFestivo($d) {
    $id        = intval($d['id'] ?? 0);
    $ejercicio = intval($d['ejercicio'] ?? 0);
    $fecha     = CadSql($d['fecha'] ?? '');
    $tipo      = in_array($d['festivo_tipo'] ?? '', ['nacional','local','convenio']) ? $d['festivo_tipo'] : 'nacional';
    $desc      = CadSql($d['festivo_desc'] ?? '');
    $reduccion = intval($d['reduccion_minutos'] ?? 0);
    $obs       = CadSql($d['observaciones'] ?? '');

    if (!$ejercicio) return '{"validacion":"warning","mensaje":"Indicar ejercicio"}';
    if (!$fecha)     return '{"validacion":"warning","mensaje":"Indicar fecha"}';
    try {
        $db = getConnection();
        if ($id > 0) {
            $s = $db->prepare("UPDATE turnos_calendario_dia SET ejercicio=?,fecha=?,festivo_tipo=?,festivo_desc=?,reduccion_minutos=?,observaciones=? WHERE id=?");
            $s->execute([$ejercicio,$fecha,$tipo,$desc,$reduccion,$obs,$id]);
        } else {
            $s = $db->prepare("INSERT INTO turnos_calendario_dia (ejercicio,fecha,festivo_tipo,festivo_desc,reduccion_minutos,observaciones) VALUES (?,?,?,?,?,?)");
            $s->execute([$ejercicio,$fecha,$tipo,$desc,$reduccion,$obs]);
            $id = $db->lastInsertId();
        }
        $db = null;
        return json_encode(['validacion'=>'ok','id'=>$id,'mensaje'=>'Festivo guardado']);
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) return '{"validacion":"warning","mensaje":"Ya existe un festivo para esa fecha en este ejercicio"}';
        return json_encode(['validacion'=>'error','error'=>$e->getMessage()]);
    }
}

function turnos_eliminarFestivo($id) {
    $id = intval($id);
    try {
        $db = getConnection();
        $db->exec("DELETE FROM turnos_calendario_dia WHERE id=$id");
        $db = null;
        return '{"validacion":"ok","mensaje":"Festivo eliminado"}';
    } catch(PDOException $e) { return json_encode(['validacion'=>'error','error'=>$e->getMessage()]); }
}

// ── REDUCCIONES DE JORNADA ───────────────────────────────────

function turnos_listarReducciones($ejercicio) {
    $ejercicio = intval($ejercicio);
    return selectPHP("SELECT * FROM turnos_reduccion WHERE ejercicio=$ejercicio ORDER BY fecha_desde");
}

function turnos_guardarReduccion($d) {
    $id      = intval($d['id'] ?? 0);
    $ej      = intval($d['ejercicio'] ?? 0);
    $desc    = CadSql($d['descripcion'] ?? '');
    $desde   = CadSql($d['fecha_desde'] ?? '');
    $hasta   = CadSql($d['fecha_hasta'] ?? '');
    $min     = intval($d['reduccion_minutos'] ?? 0);
    $sab     = intval($d['aplica_sabado'] ?? 0);
    $dom     = intval($d['aplica_domingo'] ?? 0);
    $obs     = CadSql($d['observaciones'] ?? '');

    if (!$ej)           return '{"validacion":"warning","mensaje":"Indicar ejercicio"}';
    if ($desc === '')    return '{"validacion":"warning","mensaje":"Indicar descripción"}';
    if (!$desde||!$hasta) return '{"validacion":"warning","mensaje":"Indicar fechas de inicio y fin"}';
    if (!$min)          return '{"validacion":"warning","mensaje":"Indicar minutos de reducción (>0)"}';
    try {
        $db = getConnection();
        if ($id > 0) {
            $s = $db->prepare("UPDATE turnos_reduccion SET ejercicio=?,descripcion=?,fecha_desde=?,fecha_hasta=?,reduccion_minutos=?,aplica_sabado=?,aplica_domingo=?,observaciones=? WHERE id=?");
            $s->execute([$ej,$desc,$desde,$hasta,$min,$sab,$dom,$obs,$id]);
        } else {
            $s = $db->prepare("INSERT INTO turnos_reduccion (ejercicio,descripcion,fecha_desde,fecha_hasta,reduccion_minutos,aplica_sabado,aplica_domingo,observaciones) VALUES (?,?,?,?,?,?,?,?)");
            $s->execute([$ej,$desc,$desde,$hasta,$min,$sab,$dom,$obs]);
            $id = $db->lastInsertId();
        }
        $db = null;
        return json_encode(['validacion'=>'ok','id'=>$id,'mensaje'=>'Reducción guardada']);
    } catch(PDOException $e) { return json_encode(['validacion'=>'error','error'=>$e->getMessage()]); }
}

function turnos_eliminarReduccion($id) {
    $id = intval($id);
    try {
        $db = getConnection();
        $db->exec("DELETE FROM turnos_reduccion WHERE id=$id");
        $db = null;
        return '{"validacion":"ok","mensaje":"Reducción eliminada"}';
    } catch(PDOException $e) { return json_encode(['validacion'=>'error','error'=>$e->getMessage()]); }
}

// ── CUADRANTE – HELPERS (usados por Fase 2) ──────────────────

function turnos_obtenerOCrearCuadrante($ejercicio, $mes) {
    $ejercicio = intval($ejercicio);
    $mes       = intval($mes);
    try {
        $db = getConnection();
        $row = $db->query("SELECT id FROM turnos_cuadrante WHERE ejercicio=$ejercicio AND mes=$mes")->fetch(PDO::FETCH_ASSOC);
        if ($row) { $db=null; return $row['id']; }
        $s = $db->prepare("INSERT INTO turnos_cuadrante (ejercicio,mes,estado) VALUES (?,?,'borrador')");
        $s->execute([$ejercicio,$mes]);
        $id = $db->lastInsertId();
        $db = null;
        return $id;
    } catch(PDOException $e) { return null; }
}

function turnos_obtenerCuadrante($ejercicio, $mes) {
    $ejercicio = intval($ejercicio);
    $mes       = intval($mes);
    return selectPHP("SELECT * FROM turnos_cuadrante WHERE ejercicio=$ejercicio AND mes=$mes LIMIT 1");
}

// Devuelve todos los días del cuadrante con agentes/equipos
function turnos_getDiasCuadrante($id_cuadrante) {
    $id_cuadrante = intval($id_cuadrante);
    return selectPHP("
        SELECT cd.id, cd.numagente, cd.id_equipo, cd.fecha,
               cd.codigo, cd.horas, cd.es_excepcion, cd.observaciones,
               a.nombre as agente_nombre, a.indicativo,
               e.codigo as equipo_codigo, e.nombre as equipo_nombre,
               tc.color, tc.descripcion as codigo_desc, tc.computa,
               tc.afecta_extra
        FROM turnos_cuadrante_dia cd
        JOIN agentes a ON a.numagente = cd.numagente
        JOIN turnos_equipo e ON e.id = cd.id_equipo
        LEFT JOIN turnos_codigo tc ON tc.codigo = cd.codigo
        WHERE cd.id_cuadrante = $id_cuadrante
        ORDER BY e.orden, a.nombre, cd.fecha");
}
