<?php
include("inc/seguridad.php");
include_once("inc/config.inc.php");
include_once("inc/genericasPHP.php");
include_once("inc/func_datosPHP.php");

// ── Estadísticas ──────────────────────────────────────────────
$usuarios_activos   = selectPHP("SELECT COUNT(*) as total FROM usuario WHERE activo=1");
$total_usuarios     = is_array($usuarios_activos) ? $usuarios_activos[0]['total'] : 0;

$plantillas_activas = selectPHP("SELECT COUNT(*) as total FROM plantillas_maestro WHERE estado=1");
$total_plantillas   = is_array($plantillas_activas) ? $plantillas_activas[0]['total'] : 0;

$docs_generados     = selectPHP("SELECT COUNT(*) as total FROM plantillas_documentos");
$total_docs         = is_array($docs_generados) ? $docs_generados[0]['total'] : 0;

$plantillas_inact   = selectPHP("SELECT COUNT(*) as total FROM plantillas_maestro WHERE estado=0");
$total_inactivas    = is_array($plantillas_inact) ? $plantillas_inact[0]['total'] : 0;

$repo_ficheros      = selectPHP("SELECT COUNT(*) as total FROM repositorio");
$total_repo         = is_array($repo_ficheros) ? $repo_ficheros[0]['total'] : 0;

// ── Actividad reciente (últimos 5 documentos) ─────────────────
$actividad = selectPHP("
    SELECT pd.id, pm.nombre AS plantilla, u.nombre AS usuario,
           DATE_FORMAT(pd.fecha_creacion,'%d/%m/%Y %H:%i') AS fecha
    FROM plantillas_documentos pd
    LEFT JOIN plantillas_maestro pm ON pd.cod_plantilla = pm.cod_plantilla
    LEFT JOIN usuario u ON pd.id_usuario = u.id
    ORDER BY pd.fecha_creacion DESC
    LIMIT 5
");
if (!is_array($actividad)) $actividad = [];

// ── Plantillas recientes ──────────────────────────────────────
$plantillas_rec = selectPHP("
    SELECT cod_plantilla, nombre, tipo_documento,
           DATE_FORMAT(fecha_actualizacion,'%d/%m/%Y') AS actualizado
    FROM plantillas_maestro
    WHERE estado=1
    ORDER BY fecha_actualizacion DESC
    LIMIT 5
");
if (!is_array($plantillas_rec)) $plantillas_rec = [];

$nombre_usuario = isset($_SESSION['user_descripcion']) ? htmlspecialchars($_SESSION['user_descripcion']) : 'Usuario';
$rol_usuario    = isset($_SESSION['user_rol'])          ? htmlspecialchars($_SESSION['user_rol'])          : '';
?>

<style>
    /* ── Stat cards ── */
    .dash-stat-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,132,217,.13);
        transition: transform .15s, box-shadow .15s;
        overflow: hidden;
    }
    .dash-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,132,217,.22);
    }
    .dash-stat-icon {
        width: 56px; height: 56px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .dash-stat-value {
        font-size: 2.1rem;
        font-weight: 700;
        line-height: 1;
        color: #1a2940;
    }
    .dash-stat-label {
        font-size: .82rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #7a8a9a;
        margin-top: 2px;
    }

    /* ── Welcome banner ── */
    .dash-welcome {
        background: linear-gradient(135deg, #0084D9 0%, #0066B3 60%, #004f8c 100%);
        border-radius: 12px;
        color: #fff;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 4px 18px rgba(0,102,179,.30);
    }
    .dash-welcome .welcome-name { font-size: 1.45rem; font-weight: 700; }
    .dash-welcome .welcome-sub  { font-size: .9rem; opacity: .85; margin-top: 2px; }
    .dash-welcome .welcome-date { font-size: .85rem; opacity: .75; }

    /* ── Quick actions ── */
    .dash-quick-btn {
        border: 2px solid #e3edf7;
        border-radius: 10px;
        padding: 18px 12px;
        text-align: center;
        background: #fff;
        cursor: pointer;
        transition: all .15s;
        display: block;
        color: #1a2940;
        text-decoration: none;
    }
    .dash-quick-btn:hover {
        border-color: #0084D9;
        background: linear-gradient(135deg, rgba(0,132,217,.07), rgba(0,102,179,.04));
        color: #0066B3;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,132,217,.15);
    }
    .dash-quick-btn .qbtn-icon {
        font-size: 1.8rem;
        margin-bottom: 8px;
        display: block;
    }
    .dash-quick-btn .qbtn-label {
        font-size: .82rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    /* ── Section card ── */
    .dash-section {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,.07);
    }
    .dash-section .card-header {
        border-radius: 12px 12px 0 0 !important;
        font-weight: 600;
        font-size: .93rem;
        padding: 12px 18px;
    }
    .dash-section .card-body { padding: 0; }

    /* ── Activity table ── */
    .dash-table { margin: 0; font-size: .875rem; }
    .dash-table thead th {
        background: #f4f8fd;
        border-bottom: 2px solid #e3edf7;
        color: #5a6a7a;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: 9px 14px;
    }
    .dash-table tbody td {
        padding: 9px 14px;
        border-bottom: 1px solid #f0f4f8;
        vertical-align: middle;
    }
    .dash-table tbody tr:last-child td { border-bottom: none; }
    .dash-table tbody tr:hover td { background: rgba(0,132,217,.04); }

    .dash-empty {
        text-align: center;
        padding: 30px;
        color: #b0bec8;
    }
    .dash-empty i { font-size: 2.5rem; display: block; margin-bottom: 8px; }
    .dash-empty span { font-size: .875rem; }

    /* ── Badge pill ── */
    .badge-tipo {
        font-size: .72rem;
        border-radius: 20px;
        padding: 3px 9px;
        font-weight: 600;
    }
</style>

<div class="container-fluid py-2">

    <!-- ══ Welcome banner ══════════════════════════════════════════ -->
    <div class="dash-welcome d-flex align-items-center justify-content-between flex-wrap">
        <div>
            <div class="welcome-name">
                <i class="fas fa-hand-wave mr-2" style="opacity:.8;"></i>
                Bienvenido, <?= $nombre_usuario ?>
            </div>
            <div class="welcome-sub">
                <i class="fas fa-shield-alt mr-1" style="opacity:.7;"></i>
                <?= $rol_usuario ?>
                &nbsp;·&nbsp; Panel de administración GesPol
            </div>
        </div>
        <div class="welcome-date mt-2 mt-sm-0">
            <i class="far fa-calendar-alt mr-1"></i>
            <?= strftime('%A, %d de %B de %Y') !== false
                ? mb_strtoupper(strftime('%A'), 'UTF-8') . ', ' . date('d') . ' de ' . mb_strtolower(strftime('%B'), 'UTF-8') . ' de ' . date('Y')
                : date('d/m/Y') ?>
        </div>
    </div>

    <!-- ══ Stat cards ══════════════════════════════════════════════ -->
    <div class="row mb-4">
        <!-- Usuarios activos -->
        <div class="col-6 col-md-3 mb-3">
            <div class="card dash-stat-card h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="dash-stat-icon mr-3" style="background:rgba(0,132,217,.12);">
                        <i class="fas fa-users" style="color:#0084D9;"></i>
                    </div>
                    <div>
                        <div class="dash-stat-value"><?= $total_usuarios ?></div>
                        <div class="dash-stat-label">Usuarios activos</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Plantillas activas -->
        <div class="col-6 col-md-3 mb-3">
            <div class="card dash-stat-card h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="dash-stat-icon mr-3" style="background:rgba(40,167,69,.12);">
                        <i class="fas fa-file-alt" style="color:#28a745;"></i>
                    </div>
                    <div>
                        <div class="dash-stat-value"><?= $total_plantillas ?></div>
                        <div class="dash-stat-label">Plantillas activas</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Documentos generados -->
        <div class="col-6 col-md-3 mb-3">
            <div class="card dash-stat-card h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="dash-stat-icon mr-3" style="background:rgba(23,162,184,.12);">
                        <i class="fas fa-file-pdf" style="color:#17a2b8;"></i>
                    </div>
                    <div>
                        <div class="dash-stat-value"><?= $total_docs ?></div>
                        <div class="dash-stat-label">Docs. generados</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Plantillas inactivas -->
        <div class="col-6 col-md-3 mb-3">
            <div class="card dash-stat-card h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="dash-stat-icon mr-3" style="background:rgba(255,193,7,.12);">
                        <i class="fas fa-pause-circle" style="color:#ffc107;"></i>
                    </div>
                    <div>
                        <div class="dash-stat-value"><?= $total_inactivas ?></div>
                        <div class="dash-stat-label">Plantillas inactivas</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Repositorio -->
        <div class="col-6 col-md-3 mb-3">
            <div class="card dash-stat-card h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="dash-stat-icon mr-3" style="background:rgba(111,66,193,.12);">
                        <i class="fas fa-archive" style="color:#6f42c1;"></i>
                    </div>
                    <div>
                        <div class="dash-stat-value"><?= $total_repo ?></div>
                        <div class="dash-stat-label">Ficheros repositorio</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ Acceso rápido ═══════════════════════════════════════════ -->
    <div class="row mb-4">
        <div class="col-12 mb-2">
            <h6 class="text-uppercase font-weight-bold" style="color:#5a6a7a;letter-spacing:.06em;font-size:.8rem;">
                <i class="fas fa-bolt mr-1" style="color:#0084D9;"></i> Acceso rápido
            </h6>
        </div>
        <div class="col-4 col-md-2 mb-2">
            <a href="javascript:void(0)" onclick="CargarPagina('consusuarios.php','Usuarios','far fa-user')" class="dash-quick-btn">
                <span class="qbtn-icon"><i class="fas fa-users" style="color:#0084D9;"></i></span>
                <span class="qbtn-label">Usuarios</span>
            </a>
        </div>
        <div class="col-4 col-md-2 mb-2">
            <a href="javascript:void(0)" onclick="CargarPagina('admin_plantillas.php','Plantillas','mdi mdi-file-document')" class="dash-quick-btn">
                <span class="qbtn-icon"><i class="fas fa-file-alt" style="color:#28a745;"></i></span>
                <span class="qbtn-label">Plantillas</span>
            </a>
        </div>
        <div class="col-4 col-md-2 mb-2">
            <a href="javascript:void(0)" onclick="CargarPagina('informes.php','Informes','mdi mdi-file-chart')" class="dash-quick-btn">
                <span class="qbtn-icon"><i class="fas fa-chart-bar" style="color:#17a2b8;"></i></span>
                <span class="qbtn-label">Informes</span>
            </a>
        </div>
        <div class="col-4 col-md-2 mb-2">
            <a href="javascript:void(0)" onclick="CargarPagina('repositorio.php','Repositorio','fas fa-archive')" class="dash-quick-btn">
                <span class="qbtn-icon"><i class="fas fa-archive" style="color:#6f42c1;"></i></span>
                <span class="qbtn-label">Repositorio</span>
            </a>
        </div>
    </div>

    <!-- ══ Tablas de actividad ════════════════════════════════════ -->
    <div class="row">

        <!-- Actividad reciente -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="card dash-section">
                <div class="card-header card-header-blue d-flex align-items-center">
                    <i class="fas fa-history mr-2"></i>
                    <span class="card-header-title">Actividad reciente</span>
                    <span class="ml-auto badge badge-light" style="font-size:.75rem;"><?= count($actividad) ?> docs</span>
                </div>
                <div class="card-body">
                    <?php if (empty($actividad)): ?>
                        <div class="dash-empty">
                            <i class="fas fa-inbox"></i>
                            <span>No hay documentos generados aún</span>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table dash-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Plantilla</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($actividad as $row): ?>
                                    <tr>
                                        <td class="text-muted" style="font-size:.8rem;"><?= $row['id'] ?></td>
                                        <td>
                                            <i class="fas fa-file-alt mr-1" style="color:#0084D9;font-size:.8rem;"></i>
                                            <?= htmlspecialchars($row['plantilla'] ?? '—') ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-user mr-1" style="color:#6c757d;font-size:.8rem;"></i>
                                            <?= htmlspecialchars($row['usuario'] ?? '—') ?>
                                        </td>
                                        <td class="text-muted" style="font-size:.8rem;white-space:nowrap;"><?= $row['fecha'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Plantillas recientes -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="card dash-section">
                <div class="card-header card-header-blue d-flex align-items-center">
                    <i class="fas fa-file-alt mr-2"></i>
                    <span class="card-header-title">Plantillas activas recientes</span>
                    <span class="ml-auto badge badge-light" style="font-size:.75rem;"><?= count($plantillas_rec) ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($plantillas_rec)): ?>
                        <div class="dash-empty">
                            <i class="fas fa-file-medical"></i>
                            <span>No hay plantillas activas</span>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table dash-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Actualizado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($plantillas_rec as $row): ?>
                                    <tr>
                                        <td>
                                            <code style="font-size:.75rem;background:#f0f8ff;color:#0066B3;padding:2px 5px;border-radius:3px;">
                                                <?= htmlspecialchars($row['cod_plantilla']) ?>
                                            </code>
                                        </td>
                                        <td style="font-size:.875rem;"><?= htmlspecialchars($row['nombre']) ?></td>
                                        <td>
                                            <?php
                                                $tipo = strtoupper($row['tipo_documento'] ?? '');
                                                $cls  = $tipo === 'PDF' ? 'danger' : ($tipo === 'WORD' ? 'primary' : 'secondary');
                                            ?>
                                            <span class="badge badge-<?= $cls ?> badge-tipo"><?= htmlspecialchars($tipo ?: '—') ?></span>
                                        </td>
                                        <td class="text-muted" style="font-size:.8rem;"><?= $row['actualizado'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>
