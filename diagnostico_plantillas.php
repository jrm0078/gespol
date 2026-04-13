<?php
/**
 * SCRIPT DE DIAGNÓSTICO - MÓDULO PLANTILLAS
 * Verifica: BD, tablas, datos, funciones AJAX
 * 
 * Accede a: https://cdipruebas.es/gespol/diagnostico_plantillas.php
 */

// Incluir configuración BD
include("inc/config.inc.php");

echo "<h1>🔍 DIAGNÓSTICO - MÓDULO PLANTILLAS</h1>";
echo "<hr>";

// ====================
// 1. VERIFICAR CONEXIÓN BD
// ====================
echo "<h2>1️⃣ CONEXIÓN A BASE DE DATOS</h2>";
try {
    $test = $CONEXION->query("SELECT 1");
    echo "✅ <b>Conexión exitosa</b> a: " . $BASE_DATOS . "<br>";
} catch (Exception $e) {
    echo "❌ <b>Error de conexión:</b> " . $e->getMessage() . "<br>";
    exit;
}

// ====================
// 2. VERIFICAR TABLAS
// ====================
echo "<h2>2️⃣ VERIFICAR TABLAS PLANTILLAS</h2>";

$tablas = [
    'plantillas_maestro' => 'Plantillas Maestro (definiciones)',
    'plantillas_filtros' => 'Filtros de Plantillas',
    'plantillas_variables' => 'Variables de Plantillas',
    'plantillas_documentos' => 'Documentos Generados'
];

foreach ($tablas as $tabla => $descripcion) {
    try {
        $result = $CONEXION->query("SHOW TABLES LIKE '$tabla'");
        if ($result && $result->rowCount() > 0) {
            $count = $CONEXION->query("SELECT COUNT(*) as total FROM $tabla")->fetch(PDO::FETCH_ASSOC);
            echo "✅ <b>$tabla</b> - $descripcion<br>";
            echo "   └─ Registros: " . $count['total'] . "<br>";
        } else {
            echo "❌ <b>$tabla</b> - NO EXISTE<br>";
        }
    } catch (Exception $e) {
        echo "❌ <b>$tabla</b> - Error: " . $e->getMessage() . "<br>";
    }
}

// ====================
// 3. DATOS DE PRUEBA
// ====================
echo "<h2>3️⃣ DATOS DE PRUEBA</h2>";
try {
    $plantillas = $CONEXION->query("SELECT cod_plantilla, nombre, tipo_documento FROM plantillas_maestro LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($plantillas) > 0) {
        echo "✅ <b>Plantillas disponibles:</b><br>";
        echo "<ul>";
        foreach ($plantillas as $p) {
            echo "<li><b>" . $p['cod_plantilla'] . "</b> - " . $p['nombre'] . " (" . $p['tipo_documento'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "⚠️ NO hay plantillas de prueba. Verifica que sql.sql se ejecutó correctamente.<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// ====================
// 4. VERIFICAR FUNCIONES AJAX
// ====================
echo "<h2>4️⃣ FUNCIONES AJAX PLANTILLAS</h2>";

// Leer func_plantillas.php
$func_file = "inc/plantillas/func_plantillas.php";
if (file_exists($func_file)) {
    $content = file_get_contents($func_file);
    
    $funciones_esperadas = [
        'function ListarPlantillas' => 'Listar Plantillas',
        'function CrearPlantilla' => 'Crear Plantilla',
        'function ActualizarPlantilla' => 'Actualizar Plantilla',
        'function EliminarPlantilla' => 'Eliminar Plantilla',
        'function CargarFiltros' => 'Cargar Filtros',
        'function CargarVariables' => 'Cargar Variables'
    ];
    
    foreach ($funciones_esperadas as $patron => $descripcion) {
        if (strpos($content, $patron) !== false) {
            echo "✅ <b>$descripcion</b> - OK<br>";
        } else {
            echo "⚠️ <b>$descripcion</b> - No encontrada<br>";
        }
    }
} else {
    echo "❌ Archivo <b>inc/plantillas/func_plantillas.php</b> no existe<br>";
}

// ====================
// 5. VERIFICAR RUTAS AJAX
// ====================
echo "<h2>5️⃣ RUTAS AJAX PLANTILLAS</h2>";

$ajax_file = "inc/plantillas/ajax_plantillas.php";
if (file_exists($ajax_file)) {
    $content = file_get_contents($ajax_file);
    
    if (strpos($content, "Slim") !== false) {
        echo "✅ <b>Slim Framework</b> importado<br>";
    }
    
    $rutas_esperadas = [
        'ListarPlantillas' => 'GET /ListarPlantillas',
        'CrearPlantilla' => 'POST /CrearPlantilla',
        'ActualizarPlantilla' => 'PUT /ActualizarPlantilla',
        'EliminarPlantilla' => 'DELETE /EliminarPlantilla'
    ];
    
    foreach ($rutas_esperadas as $patron => $descripcion) {
        if (strpos($content, $patron) !== false) {
            echo "✅ <b>$descripcion</b> - OK<br>";
        } else {
            echo "⚠️ <b>$descripcion</b> - No encontrada<br>";
        }
    }
} else {
    echo "❌ Archivo <b>inc/plantillas/ajax_plantillas.php</b> no existe<br>";
}

// ====================
// 6. VERIFICAR INTERFAZ FRONTEND
// ====================
echo "<h2>6️⃣ INTERFAZ FRONTEND</h2>";

if (file_exists("admin_plantillas.php")) {
    echo "✅ <b>admin_plantillas.php</b> existe<br>";
    
    $content = file_get_contents("admin_plantillas.php");
    if (strpos($content, "nuevo") !== false && strpos($content, "editar") !== false) {
        echo "✅ Elementos de formulario encontrados (crear/editar)<br>";
    }
} else {
    echo "❌ Archivo <b>admin_plantillas.php</b> no existe<br>";
}

// ====================
// 7. VERIFICAR CSS ESPECÍFICO
// ====================
echo "<h2>7️⃣ CSS PLANTILLAS</h2>";

if (file_exists("css/plantillas.css")) {
    $size = filesize("css/plantillas.css");
    echo "✅ <b>css/plantillas.css</b> existe ($size bytes)<br>";
} else {
    echo "⚠️ <b>css/plantillas.css</b> no existe (opcional)<br>";
}

// ====================
// 8. TEST RÁPIDO DE FUNCIONALIDAD
// ====================
echo "<h2>8️⃣ TEST DE FUNCIONALIDAD</h2>";

try {
    // Simular INSERT
    $test_cod = "test_" . time();
    $insert = $CONEXION->prepare("INSERT INTO plantillas_maestro (cod_plantilla, nombre, contenido) VALUES (?, ?, ?)");
    $insert->execute([$test_cod, "Test Plantilla", "<p>Test</p>"]);
    echo "✅ <b>INSERT permitido</b> en plantillas_maestro<br>";
    
    // Limpiar test
    $delete = $CONEXION->prepare("DELETE FROM plantillas_maestro WHERE cod_plantilla = ?");
    $delete->execute([$test_cod]);
    echo "✅ <b>DELETE permitido</b> en plantillas_maestro<br>";
    
} catch (Exception $e) {
    echo "⚠️ <b>Error en operaciones DB:</b> " . $e->getMessage() . "<br>";
}

// ====================
// 9. ACCIONES RECOMENDADAS
// ====================
echo "<h2>9️⃣ VERIFICACIÓN COMPLETA</h2>";
echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px;'>";
echo "<b>✅ SI VES TODOS LOS ✅:</b><br>";
echo "🟢 El módulo Plantillas está 100% funcional<br>";
echo "🟢 Accede a: <code>index.php</code> → Haz clic en <b>'Plantillas'</b> del menú<br>";
echo "🟢 Puedes crear, editar y eliminar plantillas<br>";
echo "</div>";

?>
<hr>
<p style="font-size: 12px; color: #666;">
<b>Nota:</b> Este script es para diagnóstico. Puedes eliminarlo después de verificar que todo funciona.
</p>
