<?php
/**
 * DASHBOARD DE DIAGNÓSTICO - TODOS LOS MÓDULOS
 * Resumen completo de la aplicación GESPOL
 * 
 * Accede a: https://cdipruebas.es/gespol/dashboard_diagnostico.php
 */

include("inc/config.inc.php");

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Diagnóstico GESPOL</title>
    <link href="libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f8f9fa; }
        .card { margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card-header { font-weight: bold; }
        .status-ok { color: #28a745; }
        .status-warning { color: #ffc107; }
        .status-error { color: #dc3545; }
        .test-item { padding: 8px; border-left: 4px solid #ccc; margin: 5px 0; }
        .test-item.ok { border-left-color: #28a745; background: #f0f8f4; }
        .test-item.warning { border-left-color: #ffc107; background: #fffbf0; }
        .test-item.error { border-left-color: #dc3545; background: #fdf8f7; }
        .module-section { margin: 30px 0; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h1>🔍 DASHBOARD DE DIAGNÓSTICO - GESPOL</h1>
        <p style="color: #666;">Generado: <?php echo date('d/m/Y H:i:s'); ?></p>
        <hr>

        <?php
        // ====================
        // SECCIÓN 1: BASE DE DATOS
        // ====================
        ?>
        <div class="module-section">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-database"></i> BASE DE DATOS
                </div>
                <div class="card-body">
                    <?php
                    try {
                        $test = $CONEXION->query("SELECT 1");
                        echo '<div class="test-item ok">✅ Conexión a BD: <b>' . $BASE_DATOS . '</b></div>';
                        
                        // Verificar todas las tablas
                        $tablas_requeridas = ['usuario', 'plantillas_maestro', 'plantillas_filtros', 'plantillas_variables', 'plantillas_documentos'];
                        foreach ($tablas_requeridas as $tabla) {
                            $result = $CONEXION->query("SHOW TABLES LIKE '$tabla'");
                            if ($result->rowCount() > 0) {
                                $count = $CONEXION->query("SELECT COUNT(*) as total FROM $tabla")->fetch(PDO::FETCH_ASSOC);
                                echo '<div class="test-item ok">✅ Tabla <code>' . $tabla . '</code>: ' . $count['total'] . ' registros</div>';
                            } else {
                                echo '<div class="test-item error">❌ Tabla <code>' . $tabla . '</code>: NO EXISTE</div>';
                            }
                        }
                    } catch (Exception $e) {
                        echo '<div class="test-item error">❌ Error BD: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php
        // ====================
        // SECCIÓN 2: MÓDULO USUARIOS
        // ====================
        ?>
        <div class="module-section">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-users"></i> MÓDULO USUARIOS
                </div>
                <div class="card-body">
                    <?php
                    // Verificar archivo
                    if (file_exists("consusuarios.php")) {
                        echo '<div class="test-item ok">✅ Archivo consusuarios.php existe</div>';
                    } else {
                        echo '<div class="test-item error">❌ Archivo consusuarios.php NO existe</div>';
                    }
                    
                    // Verificar funciones AJAX
                    if (file_exists("inc/func_ajax.php")) {
                        $content = file_get_contents("inc/func_ajax.php");
                        if (strpos($content, 'CargatablaUsuarios') !== false) {
                            echo '<div class="test-item ok">✅ Función CargatablaUsuarios existe</div>';
                        } else {
                            echo '<div class="test-item warning">⚠️ Función CargatablaUsuarios no encontrada</div>';
                        }
                    }
                    
                    // Verificar tabla usuario
                    try {
                        $usuarios = $CONEXION->query("SELECT COUNT(*) as total FROM usuario")->fetch(PDO::FETCH_ASSOC);
                        echo '<div class="test-item ok">✅ ' . $usuarios['total'] . ' usuarios en BD</div>';
                    } catch (Exception $e) {
                        echo '<div class="test-item error">❌ Error al leer usuarios</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php
        // ====================
        // SECCIÓN 3: MÓDULO PLANTILLAS
        // ====================
        ?>
        <div class="module-section">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-file-document"></i> MÓDULO PLANTILLAS
                </div>
                <div class="card-body">
                    <?php
                    // Verificar archivos
                    $archivos_plantillas = [
                        "admin_plantillas.php" => "Interfaz Frontend",
                        "inc/plantillas/func_plantillas.php" => "Funciones Backend",
                        "inc/plantillas/ajax_plantillas.php" => "Rutas AJAX",
                        "css/plantillas.css" => "Estilos"
                    ];
                    
                    foreach ($archivos_plantillas as $archivo => $descripcion) {
                        if (file_exists($archivo)) {
                            echo '<div class="test-item ok">✅ ' . $descripcion . ': <code>' . $archivo . '</code></div>';
                        } else {
                            echo '<div class="test-item error">❌ ' . $descripcion . ': <code>' . $archivo . '</code> NO EXISTE</div>';
                        }
                    }
                    
                    // Verificar plantillas de datos
                    try {
                        $plantillas = $CONEXION->query("SELECT COUNT(*) as total FROM plantillas_maestro WHERE estado = 1")->fetch(PDO::FETCH_ASSOC);
                        echo '<div class="test-item ok">✅ ' . $plantillas['total'] . ' plantillas activas</div>';
                    } catch (Exception $e) {
                        echo '<div class="test-item warning">⚠️ No se pudieron contar plantillas</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php
        // ====================
        // SECCIÓN 4: MÓDULO INFORMES
        // ====================
        ?>
        <div class="module-section">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-chart-bar"></i> MÓDULO INFORMES
                </div>
                <div class="card-body">
                    <?php
                    // Verificar archivo principal
                    if (file_exists("informes.php")) {
                        echo '<div class="test-item ok">✅ Archivo informes.php existe</div>';
                    } else {
                        echo '<div class="test-item error">❌ Archivo informes.php NO existe</div>';
                    }
                    
                    // Verificar funcionalidad
                    if (file_exists("informes.php")) {
                        $content = file_get_contents("informes.php");
                        
                        $funciones = ['cargarPlantilla', 'aplicarFiltro', 'generarDocumento'];
                        foreach ($funciones as $func) {
                            if (strpos($content, $func) !== false) {
                                echo '<div class="test-item ok">✅ Función JavaScript: <code>' . $func . '()</code></div>';
                            } else {
                                echo '<div class="test-item warning">⚠️ Función JavaScript: <code>' . $func . '()</code> no encontrada</div>';
                            }
                        }
                    }
                    
                    // Verificar tabla documentos
                    try {
                        $documentos = $CONEXION->query("SELECT COUNT(*) as total FROM plantillas_documentos")->fetch(PDO::FETCH_ASSOC);
                        echo '<div class="test-item ok">✅ ' . $documentos['total'] . ' documentos generados</div>';
                    } catch (Exception $e) {
                        echo '<div class="test-item warning">⚠️ Tabla plantillas_documentos no accesible</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php
        // ====================
        // SECCIÓN 5: ARCHIVOS MULTIMEDIA
        // ====================
        ?>
        <div class="module-section">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-music"></i> RECURSOS MULTIMEDIA
                </div>
                <div class="card-body">
                    <?php
                    $archivos_multimedia = [
                        "sounds/notifica.mp3" => "Sonido Notificación",
                        "sounds/alerta.mp3" => "Sonido Alerta",
                        "images/logo-icon.png" => "Logo Icono",
                        "images/logo-text.png" => "Logo Texto"
                    ];
                    
                    foreach ($archivos_multimedia as $archivo => $descripcion) {
                        if (file_exists($archivo)) {
                            $size = filesize($archivo);
                            echo '<div class="test-item ok">✅ ' . $descripcion . ': <code>' . $archivo . '</code> (' . $size . ' bytes)</div>';
                        } else {
                            echo '<div class="test-item warning">⚠️ ' . $descripcion . ': <code>' . $archivo . '</code> NO EXISTE</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php
        // ====================
        // SECCIÓN 6: LIBRERÍAS JAVASCRIPT
        // ====================
        ?>
        <div class="module-section">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-code"></i> LIBRERÍAS JAVASCRIPT (LOCAL)
                </div>
                <div class="card-body">
                    <?php
                    $librerias = [
                        "libs/jquery/dist/jquery.min.js" => "jQuery 3.6+",
                        "libs/bootstrap/dist/js/bootstrap.min.js" => "Bootstrap 4",
                        "libs/DataTables_OLD/DataTables-1.10.22/js/jquery.dataTables.min.js" => "DataTables",
                        "libs/sweetalert2/dist/sweetalert2.all.min.js" => "SweetAlert2",
                        "libs/select2/dist/js/select2.min.js" => "Select2"
                    ];
                    
                    foreach ($librerias as $archivo => $descripcion) {
                        if (file_exists($archivo)) {
                            echo '<div class="test-item ok">✅ ' . $descripcion . '</div>';
                        } else {
                            echo '<div class="test-item error">❌ ' . $descripcion . ' NO EXISTE</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php
        // ====================
        // SECCIÓN 7: ACCIONES RECOMENDADAS
        // ====================
        ?>
        <div class="module-section">
            <div class="card border-success">
                <div class="card-header bg-light border-success">
                    <i class="fas fa-check-circle"></i> ACCIONES RECOMENDADAS
                </div>
                <div class="card-body">
                    <h5>✅ Si todos los items están ✅:</h5>
                    <ol>
                        <li>Accede a <code>https://cdipruebas.es/gespol/</code></li>
                        <li>Prueba que <b>Usuarios</b> funciona (ver tabla de usuarios)</li>
                        <li>Prueba que <b>Plantillas</b> funciona (crear/editar plantillas)</li>
                        <li>Prueba que <b>Informes</b> funciona (generar documentos)</li>
                    </ol>
                    
                    <h5>⚠️ Si hay ⚠️ o ❌:</h5>
                    <ul>
                        <li>Revisa los diagnósticos específicos: <code>diagnostico_plantillas.php</code> y <code>diagnostico_informes.php</code></li>
                        <li>Verifica que se ejecutó <code>sql.sql</code> en la BD</li>
                        <li>Revisa que se subieron todos los archivos a Plesk</li>
                    </ul>

                    <h5>🗑️ Limpiar después:</h5>
                    <p>Puedes eliminar estos scripts de diagnóstico después de verificar que todo funciona.</p>
                </div>
            </div>
        </div>

        <hr>
        <p style="text-align: center; color: #666; font-size: 12px;">
            Dashboard de Diagnóstico GESPOL | Generado: <?php echo date('d/m/Y H:i:s'); ?>
        </p>
    </div>
</body>
</html>
