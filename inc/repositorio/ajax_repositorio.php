<?php
/**
 * ajax_repositorio.php
 * Punto de entrada AJAX para el módulo Repositorio
 */

ob_start(); // captura cualquier output accidental de los includes

include_once '../config.inc.php';
include_once '../genericasPHP.php';
include_once '../func_datosPHP.php';
include_once '../seguridad.php';
include_once 'func_repositorio.php';

ob_clean(); // descarta cualquier output previo (HTML de seguridad, warnings, etc.)
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'listar_directorios': listarDirectoriosAction(); break;
    case 'listar_imagenes':    listarImagenesAction();    break;
    case 'obtener':            obtenerAction();           break;
    case 'crear':              crearAction();             break;
    case 'editar':             editarAction();            break;
    case 'eliminar':           eliminarAction();          break;
    case 'cargar_tabla':       cargarTablaAction();       break;
    default:
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Acción no válida']);
}

// ─────────────────────────────────────────────
// LISTAR IMÁGENES (para TinyMCE image_list)
// ─────────────────────────────────────────────
function listarImagenesAction() {
    try {
        $db = getConnection();
        $mimeImg = "'image/jpeg','image/png','image/gif','image/webp','image/svg+xml','image/bmp'";
        $rows = $db->query(
            "SELECT descripcion, directorio, nombre_fichero, nombre_original
             FROM repositorio
             WHERE tipo IN ($mimeImg)
             ORDER BY descripcion ASC, nombre_original ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
        $list = [];
        foreach ($rows as $r) {
            $dir   = trim($r['directorio'], '/');
            $url   = REPO_BASE_URL . ($dir !== '' ? $dir . '/' : '') . $r['nombre_fichero'];
            $title = ($r['descripcion'] ?: $r['nombre_original']);
            $list[] = ['title' => $title, 'value' => $url];
        }
        echo json_encode(['ok' => true, 'data' => $list]);
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'data' => [], 'error' => $e->getMessage()]);
    }
}

// ─────────────────────────────────────────────
// LISTAR DIRECTORIOS
// ─────────────────────────────────────────────
function listarDirectoriosAction() {
    $dirs = ListarDirectoriosRepositorio();
    echo json_encode(['ok' => true, 'directorios' => $dirs]);
}

// ─────────────────────────────────────────────
// CARGAR TABLA (DataTables server-side simple)
// ─────────────────────────────────────────────
function cargarTablaAction() {
    try {
        $db = getConnection();

        $buscar = CadSql($_POST['search']['value'] ?? '');
        $start  = intval($_POST['start'] ?? 0);
        $length = intval($_POST['length'] ?? 50);
        $draw   = intval($_POST['draw'] ?? 1);

        $where = '';
        if ($buscar !== '') {
            $where = "WHERE descripcion LIKE '%$buscar%'
                      OR directorio LIKE '%$buscar%'
                      OR nombre_original LIKE '%$buscar%'";
        }

        // Order
        $cols  = ['id', 'id', 'descripcion', 'directorio', 'nombre_original', 'tipo', 'tamano', 'fecha_subida'];
        $colIdx = intval($_POST['order'][0]['column'] ?? 0);
        $colDir = ($_POST['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
        $orderCol = $cols[$colIdx] ?? 'id';

        $total    = $db->query("SELECT COUNT(*) FROM repositorio")->fetchColumn();
        $filtered = $db->query("SELECT COUNT(*) FROM repositorio $where")->fetchColumn();

        $sql  = "SELECT id, id, descripcion, directorio, nombre_original, tipo, tamano, fecha_subida
                 FROM repositorio
                 $where
                 ORDER BY $orderCol $colDir
                 LIMIT $start, $length";
        $rows = $db->query($sql)->fetchAll(PDO::FETCH_NUM);

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => intval($total),
            'recordsFiltered' => intval($filtered),
            'data'            => $rows,
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => [], 'error' => $e->getMessage()]);
    }
}

// ─────────────────────────────────────────────
// OBTENER UN REGISTRO
// ─────────────────────────────────────────────
function obtenerAction() {
    $id = intval($_POST['id'] ?? 0);
    $reg = ObtenerRepositorio($id);
    if ($reg) {
        // Añadir URL pública
        $dir = trim($reg['directorio'], '/');
        $reg['url'] = REPO_BASE_URL . ($dir !== '' ? $dir . '/' : '') . $reg['nombre_fichero'];
        echo json_encode(['ok' => true, 'data' => $reg]);
    } else {
        echo json_encode(['ok' => false, 'error' => 'Registro no encontrado']);
    }
}

// ─────────────────────────────────────────────
// CREAR
// ─────────────────────────────────────────────
function crearAction() {
    $descripcion = CadSql($_POST['descripcion'] ?? '');
    $directorio  = CadSql($_POST['directorio']  ?? '');

    if ($descripcion === '') {
        echo json_encode(['ok' => false, 'error' => 'La descripción es obligatoria']);
        return;
    }
    if (!isset($_FILES['fichero']) || $_FILES['fichero']['error'] === UPLOAD_ERR_NO_FILE) {
        echo json_encode(['ok' => false, 'error' => 'Debes seleccionar un fichero']);
        return;
    }

    $resultado = SubirFicheroRepositorio('fichero', $directorio);
    if (!$resultado['ok']) {
        echo json_encode($resultado);
        return;
    }

    try {
        $db   = getConnection();
        $stmt = $db->prepare(
            "INSERT INTO repositorio (descripcion, directorio, nombre_original, nombre_fichero, tipo, tamano)
             VALUES (:desc, :dir, :norig, :nfich, :tipo, :tam)"
        );
        $stmt->execute([
            ':desc'  => $descripcion,
            ':dir'   => $directorio,
            ':norig' => $resultado['nombre_original'],
            ':nfich' => $resultado['nombre_fichero'],
            ':tipo'  => $resultado['tipo'],
            ':tam'   => $resultado['tamano'],
        ]);
        echo json_encode(['ok' => true, 'id' => $db->lastInsertId()]);
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
}

// ─────────────────────────────────────────────
// EDITAR (solo descripción, directorio y opcionalmente nuevo fichero)
// ─────────────────────────────────────────────
function editarAction() {
    $id          = intval($_POST['id'] ?? 0);
    $descripcion = CadSql($_POST['descripcion'] ?? '');
    $directorio  = CadSql($_POST['directorio']  ?? '');

    if ($id === 0 || $descripcion === '') {
        echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
        return;
    }

    $registro = ObtenerRepositorio($id);
    if (!$registro) {
        echo json_encode(['ok' => false, 'error' => 'Registro no encontrado']);
        return;
    }

    $nuevoFichero   = $registro['nombre_fichero'];
    $nuevoOriginal  = $registro['nombre_original'];
    $nuevoTipo      = $registro['tipo'];
    $nuevoTamano    = $registro['tamano'];

    // Si se sube un fichero nuevo
    if (isset($_FILES['fichero']) && $_FILES['fichero']['error'] === UPLOAD_ERR_OK) {
        $resultado = SubirFicheroRepositorio('fichero', $directorio);
        if (!$resultado['ok']) {
            echo json_encode($resultado);
            return;
        }
        // Eliminar fichero antiguo
        $dirViejo = trim($registro['directorio'], '/');
        $rutaVieja = REPO_BASE_PATH . ($dirViejo !== '' ? $dirViejo . '/' : '') . $registro['nombre_fichero'];
        if (is_file($rutaVieja)) unlink($rutaVieja);

        $nuevoFichero  = $resultado['nombre_fichero'];
        $nuevoOriginal = $resultado['nombre_original'];
        $nuevoTipo     = $resultado['tipo'];
        $nuevoTamano   = $resultado['tamano'];
    }

    try {
        $db   = getConnection();
        $stmt = $db->prepare(
            "UPDATE repositorio SET descripcion=:desc, directorio=:dir,
             nombre_original=:norig, nombre_fichero=:nfich, tipo=:tipo, tamano=:tam
             WHERE id=:id"
        );
        $stmt->execute([
            ':desc'  => $descripcion,
            ':dir'   => $directorio,
            ':norig' => $nuevoOriginal,
            ':nfich' => $nuevoFichero,
            ':tipo'  => $nuevoTipo,
            ':tam'   => $nuevoTamano,
            ':id'    => $id,
        ]);
        echo json_encode(['ok' => true]);
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
}

// ─────────────────────────────────────────────
// ELIMINAR
// ─────────────────────────────────────────────
function eliminarAction() {
    $id = intval($_POST['id'] ?? 0);
    echo json_encode(EliminarRepositorio($id));
}
