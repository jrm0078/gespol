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

// ── CUADRANTE MENSUAL – CARGA COMPLETA PARA EDITOR ───────────

/**
 * Devuelve toda la info necesaria para renderizar el cuadrante mensual:
 *  - cabecera del cuadrante (id, estado)
 *  - equipos con sus agentes
 *  - festivos del mes
 *  - códigos activos
 *  - celdas ya grabadas
 */
function turnos_cargarCuadranteMes($ejercicio, $mes) {
    $ejercicio = intval($ejercicio);
    $mes       = intval($mes);

    try {
        $db = getConnection();

        // Obtener o crear cabecera
        $cuad = $db->query(
            "SELECT id, ejercicio, mes, estado, observaciones
             FROM turnos_cuadrante
             WHERE ejercicio=$ejercicio AND mes=$mes LIMIT 1"
        )->fetch(PDO::FETCH_ASSOC);

        if (!$cuad) {
            $db->prepare("INSERT INTO turnos_cuadrante (ejercicio,mes,estado) VALUES (?,?,'borrador')")
               ->execute([$ejercicio, $mes]);
            $cuad = [
                'id'           => $db->lastInsertId(),
                'ejercicio'    => $ejercicio,
                'mes'          => $mes,
                'estado'       => 'borrador',
                'observaciones'=> ''
            ];
        }
        $id_cuadrante = intval($cuad['id']);

        // Equipos activos con sus agentes
        $equipos = $db->query(
            "SELECT e.id, e.codigo, e.nombre, e.orden FROM turnos_equipo e WHERE e.activo=1 ORDER BY e.orden,e.nombre"
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach ($equipos as &$eq) {
            $eq_id = intval($eq['id']);
            $eq['agentes'] = $db->query(
                "SELECT tea.id as tea_id, tea.numagente, tea.orden,
                        a.nombre, a.indicativo,
                        CONCAT(a.nombre, IF(a.indicativo!='' AND a.indicativo IS NOT NULL,CONCAT(' [',a.indicativo,']'),'')) as nombre_completo
                 FROM turnos_equipo_agente tea
                 JOIN agentes a ON a.numagente=tea.numagente
                 WHERE tea.id_equipo=$eq_id AND tea.activo=1
                 ORDER BY tea.orden, a.nombre"
            )->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($eq);

        // Festivos del mes (solo del mes pedido)
        $festivos_raw = $db->query(
            "SELECT fecha, festivo_tipo, festivo_desc
             FROM turnos_calendario_dia
             WHERE ejercicio=$ejercicio
               AND MONTH(fecha)=$mes
             ORDER BY fecha"
        )->fetchAll(PDO::FETCH_ASSOC);
        $festivos = [];
        foreach ($festivos_raw as $f) {
            $festivos[$f['fecha']] = ['tipo' => $f['festivo_tipo'], 'desc' => $f['festivo_desc']];
        }

        // Códigos activos
        $codigos = $db->query(
            "SELECT codigo, descripcion, color, computa, tipo_computo, afecta_jornada, afecta_extra
             FROM turnos_codigo WHERE activo=1 ORDER BY orden, codigo"
        )->fetchAll(PDO::FETCH_ASSOC);

        // Celdas grabadas para este cuadrante
        $celdas_raw = $db->query(
            "SELECT numagente, fecha, codigo, horas, es_excepcion, observaciones
             FROM turnos_cuadrante_dia WHERE id_cuadrante=$id_cuadrante"
        )->fetchAll(PDO::FETCH_ASSOC);
        $celdas = [];
        foreach ($celdas_raw as $c) {
            $celdas[$c['numagente'] . '_' . $c['fecha']] = $c;
        }

        $db = null;
        return json_encode([
            'validacion'   => 'ok',
            'cuadrante'    => $cuad,
            'equipos'      => $equipos,
            'festivos'     => $festivos,
            'codigos'      => $codigos,
            'celdas'       => $celdas
        ]);
    } catch (PDOException $e) {
        return json_encode(['validacion' => 'error', 'error' => $e->getMessage()]);
    }
}

// ── GUARDAR UNA CELDA (agente + fecha) ───────────────────────

function turnos_guardarCelda($d) {
    $id_cuadrante = intval($d['id_cuadrante'] ?? 0);
    $numagente    = intval($d['numagente']    ?? 0);
    $id_equipo    = intval($d['id_equipo']    ?? 0);
    $fecha        = $d['fecha'] ?? '';
    $codigo       = isset($d['codigo']) && $d['codigo'] !== '' ? $d['codigo'] : null;
    $horas        = isset($d['horas']) && $d['horas'] !== '' ? floatval($d['horas']) : null;
    $es_exc       = intval($d['es_excepcion'] ?? 0);
    $obs          = $d['observaciones'] ?? '';

    if (!$id_cuadrante || !$numagente || !$id_equipo || !$fecha)
        return '{"validacion":"warning","mensaje":"Datos incompletos"}';

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha))
        return '{"validacion":"warning","mensaje":"Formato de fecha no válido"}';

    try {
        $db = getConnection();
        // Verificar que el cuadrante no está cerrado/contabilizado
        $estado = $db->query("SELECT estado FROM turnos_cuadrante WHERE id=$id_cuadrante")->fetchColumn();
        if (in_array($estado, ['cerrado','contabilizado']))
            return '{"validacion":"warning","mensaje":"El cuadrante está cerrado y no puede modificarse"}';

        // Leer el código anterior para el log de auditoría
        $ant = $db->prepare(
            "SELECT IFNULL(codigo,'') FROM turnos_cuadrante_dia
             WHERE id_cuadrante=? AND numagente=? AND fecha=? LIMIT 1"
        );
        $ant->execute([$id_cuadrante, $numagente, $fecha]);
        $cod_anterior = $ant->fetchColumn();

        $s = $db->prepare(
            "INSERT INTO turnos_cuadrante_dia (id_cuadrante,numagente,id_equipo,fecha,codigo,horas,es_excepcion,observaciones)
             VALUES (?,?,?,?,?,?,?,?)
             ON DUPLICATE KEY UPDATE
               codigo=VALUES(codigo),horas=VALUES(horas),
               es_excepcion=VALUES(es_excepcion),observaciones=VALUES(observaciones)"
        );
        $s->execute([$id_cuadrante, $numagente, $id_equipo, $fecha, $codigo, $horas, $es_exc, $obs]);

        // Auditoría: solo si cambió el código
        $cod_nuevo = $codigo ?? '';
        if ((string)$cod_anterior !== $cod_nuevo) {
            $idDia = $db->prepare(
                "SELECT id FROM turnos_cuadrante_dia
                 WHERE id_cuadrante=? AND numagente=? AND fecha=? LIMIT 1"
            );
            $idDia->execute([$id_cuadrante, $numagente, $fecha]);
            $id_dia = $idDia->fetchColumn();
            turnos_registrarAuditoria($db, 'cuadrante_dia', $id_dia, 'codigo',
                $cod_anterior, $cod_nuevo,
                "Agente:$numagente Fecha:$fecha Cuadrante:$id_cuadrante");
        }

        $db = null;
        return '{"validacion":"ok"}';
    } catch (PDOException $e) {
        return json_encode(['validacion' => 'error', 'error' => $e->getMessage()]);
    }
}

// ── GUARDAR LOTE DE CELDAS ────────────────────────────────────
// $cells = array of [numagente, id_equipo, fecha, codigo, horas, es_excepcion, observaciones]

function turnos_guardarLoteCeldas($id_cuadrante, $cells) {
    $id_cuadrante = intval($id_cuadrante);
    if (!$id_cuadrante || !is_array($cells) || empty($cells))
        return '{"validacion":"warning","mensaje":"Datos vacíos"}';

    try {
        $db = getConnection();
        $estado = $db->query("SELECT estado FROM turnos_cuadrante WHERE id=$id_cuadrante")->fetchColumn();
        if (in_array($estado, ['cerrado','contabilizado']))
            return '{"validacion":"warning","mensaje":"El cuadrante está cerrado"}';

        $db->beginTransaction();
        $s = $db->prepare(
            "INSERT INTO turnos_cuadrante_dia (id_cuadrante,numagente,id_equipo,fecha,codigo,horas,es_excepcion,observaciones)
             VALUES (?,?,?,?,?,?,?,?)
             ON DUPLICATE KEY UPDATE codigo=VALUES(codigo),horas=VALUES(horas),
               es_excepcion=VALUES(es_excepcion),observaciones=VALUES(observaciones)"
        );
        foreach ($cells as $c) {
            $s->execute([
                $id_cuadrante,
                intval($c['numagente']),
                intval($c['id_equipo']),
                $c['fecha'],
                isset($c['codigo']) && $c['codigo'] !== '' ? $c['codigo'] : null,
                isset($c['horas'])  && $c['horas']  !== '' ? floatval($c['horas']) : null,
                intval($c['es_excepcion'] ?? 0),
                $c['observaciones'] ?? ''
            ]);
        }
        $db->commit();
        $db = null;
        return '{"validacion":"ok","mensaje":"Guardado"}';
    } catch (PDOException $e) {
        if (isset($db)) { try { $db->rollBack(); } catch(Exception $e2){} }
        return json_encode(['validacion' => 'error', 'error' => $e->getMessage()]);
    }
}

// ── APLICAR PATRÓN A EQUIPO (todo el mes) ────────────────────

function turnos_aplicarPatronEquipo($id_cuadrante, $id_equipo, $codigo_patron, $dias_semana, $solo_vacias) {
    $id_cuadrante  = intval($id_cuadrante);
    $id_equipo     = intval($id_equipo);
    $codigo_patron = CadSql($codigo_patron ?? '');
    $solo_vacias   = intval($solo_vacias ?? 0);
    // $dias_semana: array de 0-6 (0=lun) que aplica

    if (!$id_cuadrante || !$id_equipo)
        return '{"validacion":"warning","mensaje":"Datos incompletos"}';

    try {
        $db = getConnection();
        $estado = $db->query("SELECT estado FROM turnos_cuadrante WHERE id=$id_cuadrante")->fetchColumn();
        if (in_array($estado, ['cerrado','contabilizado']))
            return '{"validacion":"warning","mensaje":"El cuadrante está cerrado"}';

        // Obtener ejercicio y mes
        $cuad = $db->query("SELECT ejercicio,mes FROM turnos_cuadrante WHERE id=$id_cuadrante")->fetch(PDO::FETCH_ASSOC);
        if (!$cuad) return '{"validacion":"error","error":"Cuadrante no encontrado"}';

        $ejercicio = intval($cuad['ejercicio']);
        $mes       = intval($cuad['mes']);

        // Agentes del equipo
        $agentes = $db->query(
            "SELECT numagente FROM turnos_equipo_agente WHERE id_equipo=$id_equipo AND activo=1"
        )->fetchAll(PDO::FETCH_COLUMN);

        if (empty($agentes)) return '{"validacion":"warning","mensaje":"El equipo no tiene agentes"}';

        // Días del mes
        $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ejercicio);

        $s = $db->prepare(
            "INSERT INTO turnos_cuadrante_dia (id_cuadrante,numagente,id_equipo,fecha,codigo,horas,es_excepcion)
             VALUES (?,?,?,?,?,NULL,0)
             ON DUPLICATE KEY UPDATE " .
            ($solo_vacias ? "codigo=IF(codigo IS NULL OR codigo='',VALUES(codigo),codigo)" : "codigo=VALUES(codigo),es_excepcion=0")
        );

        $diasValidos = is_array($dias_semana) && !empty($dias_semana) ? array_map('intval', $dias_semana) : null;

        $db->beginTransaction();
        $count = 0;
        for ($d = 1; $d <= $dias_mes; $d++) {
            $fecha = sprintf('%04d-%02d-%02d', $ejercicio, $mes, $d);
            $dow   = (int)date('N', strtotime($fecha)) - 1; // 0=lun..6=dom
            if ($diasValidos !== null && !in_array($dow, $diasValidos)) continue;

            foreach ($agentes as $ag) {
                $s->execute([$id_cuadrante, intval($ag), $id_equipo, $fecha, $codigo_patron ?: null]);
                $count++;
            }
        }
        $db->commit();
        $db = null;
        return json_encode(['validacion'=>'ok','mensaje'=>"Patrón aplicado a $count celdas"]);
    } catch (PDOException $e) {
        if (isset($db)) { try { $db->rollBack(); } catch(Exception $e2){} }
        return json_encode(['validacion' => 'error', 'error' => $e->getMessage()]);
    }
}

// ── LIMPIAR RANGO DE CELDAS ──────────────────────────────────

function turnos_limpiarCeldas($id_cuadrante, $cells) {
    $id_cuadrante = intval($id_cuadrante);
    if (!$id_cuadrante || !is_array($cells) || empty($cells))
        return '{"validacion":"warning","mensaje":"Datos vacíos"}';

    try {
        $db = getConnection();
        $estado = $db->query("SELECT estado FROM turnos_cuadrante WHERE id=$id_cuadrante")->fetchColumn();
        if (in_array($estado, ['cerrado','contabilizado']))
            return '{"validacion":"warning","mensaje":"El cuadrante está cerrado"}';

        $db->beginTransaction();
        $s = $db->prepare(
            "UPDATE turnos_cuadrante_dia SET codigo=NULL, horas=NULL, es_excepcion=0
             WHERE id_cuadrante=? AND numagente=? AND fecha=?"
        );
        foreach ($cells as $c) {
            $s->execute([$id_cuadrante, intval($c['numagente']), $c['fecha']]);
        }
        $db->commit();
        $db = null;
        return '{"validacion":"ok","mensaje":"Celdas limpiadas"}';
    } catch (PDOException $e) {
        if (isset($db)) { try { $db->rollBack(); } catch(Exception $e2){} }
        return json_encode(['validacion' => 'error', 'error' => $e->getMessage()]);
    }
}

// ── CALCULAR TOTALES MENSUALES POR AGENTE ────────────────────

function turnos_calcularTotalesMes($id_cuadrante) {
    $id_cuadrante = intval($id_cuadrante);
    try {
        $db = getConnection();
        $cuad = $db->query("SELECT ejercicio,mes FROM turnos_cuadrante WHERE id=$id_cuadrante")->fetch(PDO::FETCH_ASSOC);
        if (!$cuad) return json_encode(['validacion'=>'error','error'=>'Cuadrante no encontrado']);

        $ejercicio = intval($cuad['ejercicio']);
        $mes       = intval($cuad['mes']);

        // Festivos del mes
        $festivos_raw = $db->query(
            "SELECT fecha, festivo_tipo FROM turnos_calendario_dia
             WHERE ejercicio=$ejercicio AND MONTH(fecha)=$mes"
        )->fetchAll(PDO::FETCH_ASSOC);
        $festivos = [];
        foreach ($festivos_raw as $f) $festivos[$f['fecha']] = $f['festivo_tipo'];

        // Celdas con código y su tipo
        $celdas = $db->query(
            "SELECT cd.numagente, cd.fecha, cd.codigo,
                    tc.computa, tc.tipo_computo, tc.afecta_extra
             FROM turnos_cuadrante_dia cd
             LEFT JOIN turnos_codigo tc ON tc.codigo=cd.codigo
             WHERE cd.id_cuadrante=$id_cuadrante AND cd.codigo IS NOT NULL"
        )->fetchAll(PDO::FETCH_ASSOC);

        // Calcular totales por agente
        $totales = [];
        foreach ($celdas as $c) {
            $ag  = $c['numagente'];
            $fec = $c['fecha'];
            if (!isset($totales[$ag])) {
                $totales[$ag] = [
                    'numagente'              => $ag,
                    'jornadas_mes'           => 0,
                    'festivos_trabajados'    => 0,
                    'fines_semana'           => [],
                    'fines_semana_trabajados'=> 0,
                    'vacaciones_dias'        => 0,
                    'bajas_dias'             => 0,
                    'permisos_dias'          => 0,
                    'formacion_dias'         => 0,
                    'extras_dias'            => 0,
                ];
            }
            $t = &$totales[$ag];
            $dow = (int)date('N', strtotime($fec)); // 1=lun..7=dom

            // Contabilidad
            $cod = strtoupper($c['codigo']);
            if ($c['computa'] && $c['tipo_computo'] === 'normal') {
                $t['jornadas_mes']++;
                // Festivo trabajado
                if (isset($festivos[$fec])) $t['festivos_trabajados']++;
                // Fin de semana trabajado (registrar semana, no días dobles)
                if ($dow >= 6) {
                    $semana = date('W', strtotime($fec));
                    $t['fines_semana'][$semana] = true;
                }
            } elseif ($c['tipo_computo'] === 'extra') {
                $t['extras_dias']++;
            }
            // Vacaciones / bajas / permisos / formación (por código)
            if (in_array($c['codigo'], ['V']))           $t['vacaciones_dias']++;
            if (in_array($c['codigo'], ['B']))           $t['bajas_dias']++;
            if (preg_match('/^P/', $c['codigo']))        $t['permisos_dias']++;
            if (in_array($c['codigo'], ['F','Fj','E'])) $t['formacion_dias']++;

            unset($t);
        }

        // Calcular fines de semana únicos trabajados
        foreach ($totales as &$t) {
            $t['fines_semana_trabajados'] = count($t['fines_semana']);
            unset($t['fines_semana']);
        }
        unset($t);

        $db = null;
        return json_encode(['validacion'=>'ok', 'totales'=> array_values($totales)]);
    } catch (PDOException $e) {
        return json_encode(['validacion'=>'error','error'=>$e->getMessage()]);
    }
}

// ═══════════════════════════════════════════════════════════════
// CONTABILIDAD MENSUAL – FASE 5
// ═══════════════════════════════════════════════════════════════

/**
 * REGLA DECIMAL P01/P040 (Fase 7)
 * Fuente: "Notas sobre nomenclaturas y aplicación servicio.doc":
 * "Se ha determinado por el P01 y P040 que las cifras con decimales
 *  computen al alza (una jornada más) si superan 0,50 y que no se
 *  tengan en cuenta si son menores o igual a 0,50"
 *
 * Ejemplos:  2.0 → 2 | 2.4 → 2 | 2.5 → 2 | 2.51 → 3 | 2.9 → 3
 */
function _turnos_redondearP01($v) {
    $v      = floatval($v);
    $entero = (int)floor($v);
    $dec    = $v - $entero;
    return $dec > 0.50 ? $entero + 1 : $entero;
}

/**
 * Calcula todos los totales por agente leyendo el cuadrante y los guarda
 * en turnos_contabilidad_mes. Los campos manuales existentes SE PRESERVAN.
 */
function turnos_calcularContabilidad($ejercicio, $mes) {
    $ejercicio = intval($ejercicio);
    $mes       = intval($mes);

    if (!$ejercicio || $mes < 1 || $mes > 12)
        return '{"validacion":"warning","mensaje":"Ejercicio o mes no válido"}';

    try {
        $db = getConnection();

        // Obtener cuadrante
        $cuad = $db->query(
            "SELECT id, estado FROM turnos_cuadrante
             WHERE ejercicio=$ejercicio AND mes=$mes LIMIT 1"
        )->fetch(PDO::FETCH_ASSOC);

        if (!$cuad)
            return '{"validacion":"warning","mensaje":"No existe cuadrante para ese mes. Cárgalo primero desde la pantalla de Cuadrante."}';

        $id_cuadrante = intval($cuad['id']);

        // ── Festivos del mes ──────────────────────────────
        $fest_rows = $db->query(
            "SELECT fecha FROM turnos_calendario_dia
             WHERE ejercicio=$ejercicio AND MONTH(fecha)=$mes"
        )->fetchAll(PDO::FETCH_COLUMN);
        $festivos = array_flip($fest_rows); // búsqueda O(1)

        // ── Periodos de reducción que solapan con este mes ─
        $mes_pad = sprintf('%04d-%02d', $ejercicio, $mes);
        $reducciones = $db->query(
            "SELECT fecha_desde, fecha_hasta, reduccion_minutos,
                    aplica_sabado, aplica_domingo
             FROM turnos_reduccion
             WHERE ejercicio=$ejercicio
               AND DATE_FORMAT(fecha_desde,'%Y-%m') <= '$mes_pad'
               AND DATE_FORMAT(fecha_hasta,'%Y-%m')  >= '$mes_pad'"
        )->fetchAll(PDO::FETCH_ASSOC);

        // ── Todas las celdas con código del cuadrante ─────
        $celdas = $db->query(
            "SELECT cd.numagente, cd.fecha, cd.codigo,
                    IFNULL(tc.computa,0)              as computa,
                    IFNULL(tc.tipo_computo,'ninguno') as tipo_computo
             FROM turnos_cuadrante_dia cd
             LEFT JOIN turnos_codigo tc ON tc.codigo = cd.codigo
             WHERE cd.id_cuadrante = $id_cuadrante
               AND cd.codigo IS NOT NULL AND cd.codigo <> ''"
        )->fetchAll(PDO::FETCH_ASSOC);

        // ── Campos manuales ya guardados (preservar) ──────
        $manuales_raw = $db->query(
            "SELECT numagente, extras_horas, descuentos, ajuste_manual, observaciones
             FROM turnos_contabilidad_mes WHERE id_cuadrante=$id_cuadrante"
        )->fetchAll(PDO::FETCH_ASSOC);
        $manuales = [];
        foreach ($manuales_raw as $m) {
            $manuales[intval($m['numagente'])] = $m;
        }

        // ── Acumular totales por agente ───────────────────
        $totales = [];
        foreach ($celdas as $c) {
            $ag  = intval($c['numagente']);
            $fec = $c['fecha'];
            $cod = $c['codigo'];

            if (!isset($totales[$ag])) {
                $totales[$ag] = [
                    'jornadas_mes'           => 0,
                    'festivos_trabajados'    => 0,
                    '_semanas_fs'            => [],  // auxiliar: set de semanas ISO
                    'fines_semana_trabajados'=> 0,
                    'vacaciones_dias'        => 0,
                    'bajas_dias'             => 0,
                    'permisos_dias'          => 0,
                    'formacion_dias'         => 0,
                    'horas_reduccion'        => 0.0,
                    'p01_jornadas'           => 0,
                    'p040_jornadas'          => 0,
                ];
            }
            $t = &$totales[$ag];

            $dt  = new DateTime($fec);
            $dow = (int)$dt->format('N'); // 1=lun … 7=dom

            // — Jornada normal trabajada —
            if ($c['computa'] && $c['tipo_computo'] === 'normal') {
                $t['jornadas_mes']++;

                // ¿Es festivo?
                if (isset($festivos[$fec])) $t['festivos_trabajados']++;

                // ¿Es sábado(6) o domingo(7)?
                if ($dow >= 6) {
                    // Clave única por fin de semana (año ISO + semana ISO)
                    $t['_semanas_fs'][$dt->format('oW')] = true;
                }

                // ¿Cae en un periodo de reducción de jornada?
                foreach ($reducciones as $r) {
                    if ($fec < $r['fecha_desde'] || $fec > $r['fecha_hasta']) continue;
                    if ($dow === 6 && !intval($r['aplica_sabado']))  continue;
                    if ($dow === 7 && !intval($r['aplica_domingo'])) continue;
                    $t['horas_reduccion'] += intval($r['reduccion_minutos']) / 60.0;
                }
            }

            // — Clasificación por código —
            // Vacaciones
            if ($cod === 'V')         { $t['vacaciones_dias']++; }
            // Bajas
            elseif ($cod === 'B')     { $t['bajas_dias']++; }
            // Permisos: P, Pa, Ps, Pc, Pf, Pas, P01, P040 …
            elseif ($cod[0] === 'P')  { $t['permisos_dias']++; }

            // Formación / escuela / jefatura / onomástica
            if (in_array($cod, ['F','Fj','E','Sto','Jf'])) {
                $t['formacion_dias']++;
            }

            // P01 y P040 contados por separado (regla decimal ≥0,50)
            if ($cod === 'P01')  $t['p01_jornadas']++;
            if ($cod === 'P040') $t['p040_jornadas']++;

            unset($t);
        }

        // ── Finalizar totales ─────────────────────────────
        foreach ($totales as &$t) {
            $t['fines_semana_trabajados'] = count($t['_semanas_fs']);
            unset($t['_semanas_fs']);

            // ── REGLA P01/P040 (Fase 7) ───────────────────
            // Fuente: "Notas sobre nomenclaturas":
            // "Las cifras con decimales computan al alza si superan 0,50;
            //  si son menores o iguales a 0,50 no se tienen en cuenta."
            $t['p01_jornadas']  = _turnos_redondearP01($t['p01_jornadas']);
            $t['p040_jornadas'] = _turnos_redondearP01($t['p040_jornadas']);
        }
        unset($t);

        // ── Garantizar fila para TODOS los agentes activos ─
        $todos_agentes = $db->query(
            "SELECT DISTINCT numagente FROM turnos_equipo_agente WHERE activo=1"
        )->fetchAll(PDO::FETCH_COLUMN);
        foreach ($todos_agentes as $ag) {
            $ag = intval($ag);
            if (!isset($totales[$ag])) {
                $totales[$ag] = [
                    'jornadas_mes'           => 0,
                    'festivos_trabajados'    => 0,
                    'fines_semana_trabajados'=> 0,
                    'vacaciones_dias'        => 0,
                    'bajas_dias'             => 0,
                    'permisos_dias'          => 0,
                    'formacion_dias'         => 0,
                    'horas_reduccion'        => 0.0,
                    'p01_jornadas'           => 0,
                    'p040_jornadas'          => 0,
                ];
            }
        }

        // ── Upsert: actualiza calculados, preserva manuales ─
        $s = $db->prepare(
            "INSERT INTO turnos_contabilidad_mes
               (id_cuadrante, numagente,
                jornadas_mes, festivos_trabajados, fines_semana_trabajados,
                vacaciones_dias, bajas_dias, permisos_dias, formacion_dias,
                horas_reduccion, p01_jornadas, p040_jornadas,
                extras_horas, descuentos, ajuste_manual, observaciones,
                calculado_en)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())
             ON DUPLICATE KEY UPDATE
               jornadas_mes            = VALUES(jornadas_mes),
               festivos_trabajados     = VALUES(festivos_trabajados),
               fines_semana_trabajados = VALUES(fines_semana_trabajados),
               vacaciones_dias         = VALUES(vacaciones_dias),
               bajas_dias              = VALUES(bajas_dias),
               permisos_dias           = VALUES(permisos_dias),
               formacion_dias          = VALUES(formacion_dias),
               horas_reduccion         = VALUES(horas_reduccion),
               p01_jornadas            = VALUES(p01_jornadas),
               p040_jornadas           = VALUES(p040_jornadas),
               calculado_en            = NOW()"
            /* Los campos manuales NO se tocan en el UPDATE para preservarlos */
        );

        $db->beginTransaction();
        foreach ($totales as $ag => $t) {
            $man = $manuales[$ag] ?? [];
            $s->execute([
                $id_cuadrante,
                $ag,
                $t['jornadas_mes'],
                $t['festivos_trabajados'],
                $t['fines_semana_trabajados'],
                $t['vacaciones_dias'],
                $t['bajas_dias'],
                $t['permisos_dias'],
                $t['formacion_dias'],
                round($t['horas_reduccion'], 2),
                $t['p01_jornadas'],
                $t['p040_jornadas'],
                // Manuales: solo se usan al insertar (no en UPDATE)
                floatval($man['extras_horas']  ?? 0),
                floatval($man['descuentos']    ?? 0),
                floatval($man['ajuste_manual'] ?? 0),
                $man['observaciones'] ?? '',
            ]);
        }
        $db->commit();
        $db = null;

        return json_encode([
            'validacion'    => 'ok',
            'mensaje'       => 'Contabilidad calculada para ' . count($totales) . ' agente(s)',
            'total_agentes' => count($totales),
        ]);
    } catch (PDOException $e) {
        if (isset($db)) { try { $db->rollBack(); } catch (Exception $e2) {} }
        return json_encode(['validacion' => 'error', 'error' => $e->getMessage()]);
    }
}

/**
 * Devuelve la contabilidad mensual completa para mostrar en pantalla.
 */
function turnos_cargarContabilidad($ejercicio, $mes) {
    $ejercicio = intval($ejercicio);
    $mes       = intval($mes);

    try {
        $db = getConnection();

        $cuad = $db->query(
            "SELECT id, ejercicio, mes, estado,
                    IFNULL(observaciones,'') as observaciones,
                    fecha_cierre, usuario_cierre
             FROM turnos_cuadrante
             WHERE ejercicio=$ejercicio AND mes=$mes LIMIT 1"
        )->fetch(PDO::FETCH_ASSOC);

        if (!$cuad)
            return json_encode([
                'validacion' => 'warning',
                'mensaje'    => 'No existe cuadrante para ese mes. Accede primero al Cuadrante mensual y cárgalo.',
            ]);

        $id_cuadrante = intval($cuad['id']);

        // Contabilidad con datos de agente y equipo
        $filas = $db->query(
            "SELECT cm.id, cm.numagente,
                    cm.jornadas_mes, cm.festivos_trabajados, cm.fines_semana_trabajados,
                    cm.vacaciones_dias, cm.bajas_dias, cm.permisos_dias, cm.formacion_dias,
                    cm.horas_reduccion, cm.p01_jornadas, cm.p040_jornadas,
                    cm.extras_horas, cm.descuentos, cm.ajuste_manual,
                    IFNULL(cm.observaciones,'') as observaciones,
                    cm.calculado_en, cm.editado_en,
                    IFNULL(cm.editado_por,'') as editado_por,
                    a.nombre as agente_nombre,
                    IFNULL(a.indicativo,'') as indicativo,
                    CONCAT(a.nombre,
                           IF(a.indicativo IS NOT NULL AND a.indicativo <> '',
                              CONCAT(' [',a.indicativo,']'), '')) as nombre_completo,
                    IFNULL(e.codigo,'–')  as equipo_codigo,
                    IFNULL(e.nombre,'–')  as equipo_nombre,
                    IFNULL(e.orden, 99)   as equipo_orden,
                    IFNULL(e.id, 0)       as equipo_id
             FROM turnos_contabilidad_mes cm
             JOIN agentes a ON a.numagente = cm.numagente
             LEFT JOIN turnos_equipo_agente tea
                    ON tea.numagente = cm.numagente AND tea.activo = 1
             LEFT JOIN turnos_equipo e ON e.id = tea.id_equipo
             WHERE cm.id_cuadrante = $id_cuadrante
             ORDER BY equipo_orden, a.nombre"
        )->fetchAll(PDO::FETCH_ASSOC);

        $db = null;

        // Días laborables teóricos por mes 2026 (del documento)
        $dias_teoricos = [
            1=>20, 2=>20, 3=>22, 4=>19, 5=>20, 6=>22,
            7=>20, 8=>21, 9=>21,10=>21,11=>20,12=>18,
        ];

        return json_encode([
            'validacion'    => 'ok',
            'cuadrante'     => $cuad,
            'filas'         => $filas,
            'dias_teoricos' => $dias_teoricos[$mes] ?? 0,
            'calculado'     => count($filas) > 0,
        ]);
    } catch (PDOException $e) {
        return json_encode(['validacion' => 'error', 'error' => $e->getMessage()]);
    }
}

/**
 * Guarda SOLO los campos manuales de una fila de contabilidad.
 * Los campos calculados no se modifican.
 */
function turnos_guardarFilaContabilidad($d) {
    $id           = intval($d['id']            ?? 0);
    $extras_horas = floatval($d['extras_horas'] ?? 0);
    $descuentos   = floatval($d['descuentos']   ?? 0);
    $ajuste       = floatval($d['ajuste_manual'] ?? 0);
    $obs          = $d['observaciones']          ?? '';
    $usuario      = isset($_SESSION['user_descripcion'])
                    ? $_SESSION['user_descripcion'] : 'sistema';

    if (!$id) return '{"validacion":"warning","mensaje":"ID de fila no válido"}';

    try {
        $db = getConnection();
        $s  = $db->prepare(
            "UPDATE turnos_contabilidad_mes
             SET extras_horas = ?,
                 descuentos   = ?,
                 ajuste_manual= ?,
                 observaciones= ?,
                 editado_en   = NOW(),
                 editado_por  = ?
             WHERE id = ?"
        );
        $s->execute([$extras_horas, $descuentos, $ajuste, $obs, $usuario, $id]);

        // Auditoría: registrar los campos manuales modificados
        $desc_aud = "Contabilidad fila id:$id";
        turnos_registrarAuditoria($db, 'contabilidad_mes', $id, 'extras_horas',   '', $extras_horas, $desc_aud);
        turnos_registrarAuditoria($db, 'contabilidad_mes', $id, 'descuentos',     '', $descuentos,   $desc_aud);
        turnos_registrarAuditoria($db, 'contabilidad_mes', $id, 'ajuste_manual',  '', $ajuste,       $desc_aud);
        turnos_registrarAuditoria($db, 'contabilidad_mes', $id, 'observaciones',  '', $obs,          $desc_aud);

        $db = null;
        return '{"validacion":"ok","mensaje":"Guardado"}';
    } catch (PDOException $e) {
        return json_encode(['validacion' => 'error', 'error' => $e->getMessage()]);
    }
}

/**
 * Cierra el mes: pasa el estado del cuadrante a 'cerrado'.
 * El cuadrante ya no puede editarse pero la contabilidad sí.
 */
function turnos_cerrarMes($ejercicio, $mes) {
    $ejercicio = intval($ejercicio);
    $mes       = intval($mes);
    $usuario   = isset($_SESSION['user_descripcion'])
                 ? $_SESSION['user_descripcion'] : 'sistema';
    try {
        $db = getConnection();

        // Leer estado actual antes del cambio
        $cuad = $db->query(
            "SELECT id, IFNULL(estado,'borrador') as estado
             FROM turnos_cuadrante WHERE ejercicio=$ejercicio AND mes=$mes LIMIT 1"
        )->fetch(PDO::FETCH_ASSOC);

        $s  = $db->prepare(
            "UPDATE turnos_cuadrante
             SET estado='cerrado', fecha_cierre=NOW(), usuario_cierre=?
             WHERE ejercicio=? AND mes=?"
        );
        $s->execute([$usuario, $ejercicio, $mes]);

        if ($cuad) {
            turnos_registrarAuditoria($db, 'cuadrante', $cuad['id'], 'estado',
                $cuad['estado'], 'cerrado',
                "Cierre manual. Ejercicio:$ejercicio Mes:$mes");
        }

        $db = null;
        return '{"validacion":"ok","mensaje":"Mes cerrado correctamente"}';
    } catch (PDOException $e) {
        return json_encode(['validacion' => 'error', 'error' => $e->getMessage()]);
    }
}

/**
 * Reabre el mes: vuelve el estado del cuadrante a 'borrador'.
 */
function turnos_reabrirMes($ejercicio, $mes) {
    $ejercicio = intval($ejercicio);
    $mes       = intval($mes);
    $usuario   = isset($_SESSION['user_descripcion'])
                 ? $_SESSION['user_descripcion'] : 'sistema';
    try {
        $db = getConnection();

        $cuad = $db->query(
            "SELECT id, IFNULL(estado,'borrador') as estado
             FROM turnos_cuadrante WHERE ejercicio=$ejercicio AND mes=$mes LIMIT 1"
        )->fetch(PDO::FETCH_ASSOC);

        $s = $db->prepare(
            "UPDATE turnos_cuadrante
             SET estado='borrador', fecha_cierre=NULL, usuario_cierre=NULL
             WHERE ejercicio=? AND mes=?"
        );
        $s->execute([$ejercicio, $mes]);

        if ($cuad) {
            turnos_registrarAuditoria($db, 'cuadrante', $cuad['id'], 'estado',
                $cuad['estado'], 'borrador',
                "Reapertura. Ejercicio:$ejercicio Mes:$mes");
        }

        $db = null;
        return '{"validacion":"ok","mensaje":"Mes reabierto como borrador"}';
    } catch (PDOException $e) {
        return json_encode(['validacion' => 'error', 'error' => $e->getMessage()]);
    }
}

/**
 * Contabiliza el mes: estado → 'contabilizado'.
 * Requiere que existan filas de contabilidad ya calculadas.
 */
function turnos_contabilizarMes($ejercicio, $mes) {
    $ejercicio = intval($ejercicio);
    $mes       = intval($mes);
    $usuario   = isset($_SESSION['user_descripcion'])
                 ? $_SESSION['user_descripcion'] : 'sistema';
    try {
        $db = getConnection();

        $cuad = $db->query(
            "SELECT id, IFNULL(estado,'borrador') as estado
             FROM turnos_cuadrante WHERE ejercicio=$ejercicio AND mes=$mes LIMIT 1"
        )->fetch(PDO::FETCH_ASSOC);

        if (!$cuad)
            return '{"validacion":"warning","mensaje":"No existe cuadrante para ese mes"}';

        $nfilas = $db->query(
            "SELECT COUNT(*) FROM turnos_contabilidad_mes WHERE id_cuadrante=" . intval($cuad['id'])
        )->fetchColumn();

        if ($nfilas == 0)
            return '{"validacion":"warning","mensaje":"Antes de contabilizar, pulse Recalcular para generar los datos"}';

        $s = $db->prepare(
            "UPDATE turnos_cuadrante
             SET estado='contabilizado', fecha_cierre=NOW(), usuario_cierre=?
             WHERE ejercicio=? AND mes=?"
        );
        $s->execute([$usuario, $ejercicio, $mes]);

        turnos_registrarAuditoria($db, 'cuadrante', $cuad['id'], 'estado',
            $cuad['estado'], 'contabilizado',
            "Contabilización. Ejercicio:$ejercicio Mes:$mes");

        $db = null;
        return '{"validacion":"ok","mensaje":"Mes contabilizado correctamente"}';
    } catch (PDOException $e) {
        return json_encode(['validacion' => 'error', 'error' => $e->getMessage()]);
    }
}

// ═══════════════════════════════════════════════════════════════
// AUDITORÍA – FASE 6
// ═══════════════════════════════════════════════════════════════

/**
 * Registra un cambio en la tabla turnos_auditoria.
 * Llamar con una conexión PDO ya abierta para no abrir una segunda.
 *
 * @param PDO    $db          Conexión activa
 * @param string $entidad     Nombre de la tabla o entidad (ej: 'cuadrante_dia')
 * @param int    $id_entidad  ID del registro afectado
 * @param string $campo       Nombre del campo modificado
 * @param string $anterior    Valor anterior (puede ser vacío)
 * @param string $nuevo       Valor nuevo
 * @param string $obs         Observación opcional
 */
function turnos_registrarAuditoria($db, $entidad, $id_entidad, $campo, $anterior, $nuevo, $obs = '') {
    $usuario = isset($_SESSION['user_descripcion']) ? $_SESSION['user_descripcion'] : 'sistema';
    try {
        $s = $db->prepare(
            "INSERT INTO turnos_auditoria
               (usuario, entidad, id_entidad, campo, valor_anterior, valor_nuevo, observaciones)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $s->execute([$usuario, $entidad, intval($id_entidad), $campo,
                     (string)$anterior, (string)$nuevo, (string)$obs]);
    } catch (Exception $e) {
        // La auditoría nunca debe romper la operación principal
    }
}

/**
 * Devuelve registros de auditoría con filtros opcionales.
 * Paginación: 100 filas por página.
 */
function turnos_cargarAuditoria($filtros = []) {
    $pagina   = max(1, intval($filtros['pagina']   ?? 1));
    $entidad  = $filtros['entidad']  ?? '';
    $usuario  = $filtros['usuario']  ?? '';
    $desde    = $filtros['desde']    ?? '';
    $hasta    = $filtros['hasta']    ?? '';
    $busqueda = $filtros['busqueda'] ?? '';

    $por_pagina = 100;
    $offset     = ($pagina - 1) * $por_pagina;

    try {
        $db = getConnection();

        $where = '1=1';
        $params = [];

        if ($entidad !== '') {
            $where .= ' AND entidad = ?';
            $params[] = $entidad;
        }
        if ($usuario !== '') {
            $where .= ' AND usuario LIKE ?';
            $params[] = '%' . $usuario . '%';
        }
        if ($desde !== '') {
            $where .= ' AND DATE(fecha_hora) >= ?';
            $params[] = $desde;
        }
        if ($hasta !== '') {
            $where .= ' AND DATE(fecha_hora) <= ?';
            $params[] = $hasta;
        }
        if ($busqueda !== '') {
            $where .= ' AND (valor_anterior LIKE ? OR valor_nuevo LIKE ? OR observaciones LIKE ?)';
            $b = '%' . $busqueda . '%';
            $params[] = $b; $params[] = $b; $params[] = $b;
        }

        // Total sin paginar
        $total = $db->prepare("SELECT COUNT(*) FROM turnos_auditoria WHERE $where");
        $total->execute($params);
        $total_filas = intval($total->fetchColumn());

        // Filas paginadas
        $stmt = $db->prepare(
            "SELECT id, fecha_hora, usuario, entidad, id_entidad,
                    campo, valor_anterior, valor_nuevo,
                    IFNULL(observaciones,'') as observaciones
             FROM turnos_auditoria
             WHERE $where
             ORDER BY id DESC
             LIMIT $por_pagina OFFSET $offset"
        );
        $stmt->execute($params);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lista de entidades distintas para el filtro
        $entidades = $db->query(
            "SELECT DISTINCT entidad FROM turnos_auditoria ORDER BY entidad"
        )->fetchAll(PDO::FETCH_COLUMN);

        $db = null;
        return json_encode([
            'validacion'   => 'ok',
            'filas'        => $filas,
            'total'        => $total_filas,
            'pagina'       => $pagina,
            'por_pagina'   => $por_pagina,
            'total_paginas'=> max(1, ceil($total_filas / $por_pagina)),
            'entidades'    => $entidades,
        ]);
    } catch (PDOException $e) {
        return json_encode(['validacion' => 'error', 'error' => $e->getMessage()]);
    }
}

