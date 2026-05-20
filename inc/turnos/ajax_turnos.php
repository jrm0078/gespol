<?php
ob_start();
include_once '../config.inc.php';
include_once '../genericasPHP.php';
include_once '../func_datosPHP.php';
include_once 'func_turnos.php';
include_once '../seguridad.php';
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$data   = $_POST;

switch ($action) {

    // ── EJERCICIOS ──────────────────────────────────────────
    case 'listar_ejercicios':
        $rows = turnos_listarEjercicios();
        echo json_encode(['validacion'=>'ok','data'=>$rows]);
        break;

    case 'guardar_ejercicio':
        echo turnos_guardarEjercicio($data);
        break;

    // ── EQUIPOS ─────────────────────────────────────────────
    case 'listar_equipos':
        $rows = turnos_listarEquipos();
        echo json_encode(['validacion'=>'ok','data'=>$rows]);
        break;

    case 'listar_equipos_activos':
        $rows = turnos_listarEquipos(true);
        echo json_encode(['validacion'=>'ok','data'=>$rows]);
        break;

    case 'obtener_equipo':
        $rows = turnos_obtenerEquipo(intval($_POST['id'] ?? 0));
        echo json_encode(['validacion'=>'ok','data'=>$rows]);
        break;

    case 'guardar_equipo':
        echo turnos_guardarEquipo($data);
        break;

    case 'eliminar_equipo':
        echo turnos_eliminarEquipo(intval($_POST['id'] ?? 0));
        break;

    // ── EQUIPO–AGENTE ────────────────────────────────────────
    case 'listar_agentes_equipo':
        $rows = turnos_listarAgentesEquipo(intval($_POST['id_equipo'] ?? 0));
        echo json_encode(['validacion'=>'ok','data'=>$rows]);
        break;

    case 'asignar_agente':
        echo turnos_asignarAgente(
            intval($_POST['id_equipo']   ?? 0),
            intval($_POST['numagente']   ?? 0),
            intval($_POST['orden']       ?? 99),
            $_POST['fecha_desde'] ?? ''
        );
        break;

    case 'quitar_agente':
        echo turnos_quitarAgente(intval($_POST['id'] ?? 0));
        break;

    case 'combo_agentes':
        $rows = turnos_comboAgentes(intval($_POST['id_equipo'] ?? 0));
        echo json_encode(['validacion'=>'ok','data'=>$rows]);
        break;

    // ── CÓDIGOS ──────────────────────────────────────────────
    case 'listar_codigos':
        $rows = turnos_listarCodigos();
        echo json_encode(['validacion'=>'ok','data'=>$rows]);
        break;

    case 'guardar_codigo':
        echo turnos_guardarCodigo($data);
        break;

    case 'eliminar_codigo':
        echo turnos_eliminarCodigo(intval($_POST['id'] ?? 0));
        break;

    // ── CALENDARIO ───────────────────────────────────────────
    case 'listar_festivos':
        $rows = turnos_listarFestivos(intval($_POST['ejercicio'] ?? 2026));
        echo json_encode(['validacion'=>'ok','data'=>$rows]);
        break;

    case 'guardar_festivo':
        echo turnos_guardarFestivo($data);
        break;

    case 'eliminar_festivo':
        echo turnos_eliminarFestivo(intval($_POST['id'] ?? 0));
        break;

    case 'listar_reducciones':
        $rows = turnos_listarReducciones(intval($_POST['ejercicio'] ?? 2026));
        echo json_encode(['validacion'=>'ok','data'=>$rows]);
        break;

    case 'guardar_reduccion':
        echo turnos_guardarReduccion($data);
        break;

    case 'eliminar_reduccion':
        echo turnos_eliminarReduccion(intval($_POST['id'] ?? 0));
        break;

    // ── CUADRANTE ────────────────────────────────────────────
    case 'obtener_cuadrante':
        $ej  = intval($_POST['ejercicio'] ?? 0);
        $mes = intval($_POST['mes'] ?? 0);
        $rows = turnos_obtenerCuadrante($ej, $mes);
        echo json_encode(['validacion'=>'ok','data'=>$rows]);
        break;

    case 'cargar_cuadrante_mes':
        $ej  = intval($_POST['ejercicio'] ?? 0);
        $mes = intval($_POST['mes'] ?? 0);
        echo turnos_cargarCuadranteMes($ej, $mes);
        break;

    case 'guardar_celda':
        echo turnos_guardarCelda($_POST);
        break;

    case 'guardar_lote_celdas':
        $id_cuadrante = intval($_POST['id_cuadrante'] ?? 0);
        $cells        = json_decode($_POST['cells'] ?? '[]', true);
        echo turnos_guardarLoteCeldas($id_cuadrante, $cells);
        break;

    case 'aplicar_patron_equipo':
        $id_cuadrante  = intval($_POST['id_cuadrante']  ?? 0);
        $id_equipo     = intval($_POST['id_equipo']     ?? 0);
        $codigo_patron = $_POST['codigo_patron'] ?? '';
        $dias_semana   = json_decode($_POST['dias_semana'] ?? '[0,1,2,3,4,5,6]', true);
        $solo_vacias   = intval($_POST['solo_vacias']   ?? 0);
        echo turnos_aplicarPatronEquipo($id_cuadrante, $id_equipo, $codigo_patron, $dias_semana, $solo_vacias);
        break;

    case 'limpiar_celdas':
        $id_cuadrante = intval($_POST['id_cuadrante'] ?? 0);
        $cells        = json_decode($_POST['cells'] ?? '[]', true);
        echo turnos_limpiarCeldas($id_cuadrante, $cells);
        break;

    case 'calcular_totales_mes':
        $id_cuadrante = intval($_POST['id_cuadrante'] ?? 0);
        echo turnos_calcularTotalesMes($id_cuadrante);
        break;

    case 'get_dias_cuadrante':
        $id = intval($_POST['id_cuadrante'] ?? 0);
        $rows = turnos_getDiasCuadrante($id);
        echo json_encode(['validacion'=>'ok','data'=>$rows]);
        break;

    // ── CONTABILIDAD MENSUAL ─────────────────────────────
    case 'calcular_contabilidad':
        echo turnos_calcularContabilidad(
            intval($_POST['ejercicio'] ?? 0),
            intval($_POST['mes']       ?? 0)
        );
        break;

    case 'cargar_contabilidad':
        echo turnos_cargarContabilidad(
            intval($_POST['ejercicio'] ?? 0),
            intval($_POST['mes']       ?? 0)
        );
        break;

    case 'guardar_fila_contabilidad':
        echo turnos_guardarFilaContabilidad($_POST);
        break;

    case 'cerrar_mes':
        echo turnos_cerrarMes(
            intval($_POST['ejercicio'] ?? 0),
            intval($_POST['mes']       ?? 0)
        );
        break;

    case 'reabrir_mes':
        echo turnos_reabrirMes(
            intval($_POST['ejercicio'] ?? 0),
            intval($_POST['mes']       ?? 0)
        );
        break;

    case 'contabilizar_mes':
        echo turnos_contabilizarMes(
            intval($_POST['ejercicio'] ?? 0),
            intval($_POST['mes']       ?? 0)
        );
        break;

    // ── AUDITORÍA – FASE 6 ───────────────────────────────
    case 'cargar_auditoria':
        echo turnos_cargarAuditoria([
            'pagina'   => intval($_POST['pagina']   ?? 1),
            'entidad'  => $_POST['entidad']  ?? '',
            'usuario'  => $_POST['usuario']  ?? '',
            'desde'    => $_POST['desde']    ?? '',
            'hasta'    => $_POST['hasta']    ?? '',
            'busqueda' => $_POST['busqueda'] ?? '',
        ]);
        break;

    default:
        echo json_encode(['validacion'=>'error','error'=>'Acción no reconocida: '.$action]);
}
