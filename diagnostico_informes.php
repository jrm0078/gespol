<?php
/**
 * SCRIPT DE DIAGNÓSTICO - MÓDULO INFORMES
 * Verifica: BD, tablas, datos, funciones AJAX
 * 
 * Accede a: https://cdipruebas.es/gespol/diagnostico_informes.php
 */

// Incluir configuración BD
include("inc/config.inc.php");

echo "<h1>🔍 DIAGNÓSTICO - MÓDULO INFORMES</h1>";
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
// 2. VERIFICAR TABLAS DEPENDENCIAS
// ====================
echo "<h2>2️⃣ VERIFICAR TABLAS DEPENDENCIAS</h2>";

$tablas = [
    'usuario' => 'Usuarios (auténtica)',
    'plantillas_maestro' => 'Plantillas Maestro (fuente)',
    'plantillas_filtros' => 'Filtros (búsqueda)',
    'plantillas_variables' => 'Variables (reemplazo)',
    'plantillas_documentos' => 'Documentos (guardado)'
];

foreach ($tablas as $tabla => $descripcion) {
    try {
        $result = $CONEXION->query("SHOW TABLES LIKE '$tabla'");
        if ($result && $result->rowCount() > 0) {
            $count = $CONEXION->query("SELECT COUNT(*) as total FROM $tabla")->fetch(PDO::FETCH_ASSOC);
            echo "✅ <b>$tabla</b> - $descripcion<br>";
            echo "   └─ Registros: " . $count['total'] . "<br>";
        } else {
            echo "❌ <b>$tabla</b> - NO EXISTE (REQUERIDA)<br>";
        }
    } catch (Exception $e) {
        echo "❌ <b>$tabla</b> - Error: " . $e->getMessage() . "<br>";
    }
}

// ====================
// 3. VERIFICAR PLANTILLAS DISPONIBLES
// ====================
echo "<h2>3️⃣ PLANTILLAS DISPONIBLES PARA INFORMES</h2>";
try {
    $plantillas = $CONEXION->query(
        "SELECT cod_plantilla, nombre, tipo_documento, estado FROM plantillas_maestro WHERE estado = 1"
    )->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($plantillas) > 0) {
        echo "✅ <b>" . count($plantillas) . " plantillas activas:</b><br>";
        echo "<ul>";
        foreach ($plantillas as $p) {
            echo "<li>";
            echo "<b>" . $p['cod_plantilla'] . "</b> - " . $p['nombre'] . " ";
            echo "(<code>" . $p['tipo_documento'] . "</code>)";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "⚠️ <b>NO hay plantillas activas</b><br>";
        echo "Solución: Crea al menos una plantilla en el módulo 'Plantillas'<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// ====================
// 4. VERIFICAR FUNCIONES JAVASCRIPT
// ====================
echo "<h2>4️⃣ FUNCIONES JAVASCRIPT REQUERIDAS</h2>";

if (file_exists("informes.php")) {
    $content = file_get_contents("informes.php");
    
    $funciones_esperadas = [
        'cargarPlantilla' => 'Cargar Plantilla',
        'aplicarFiltro' => 'Aplicar Filtros',
        'generarDocumento' => 'Generar Documento',
        'descargarDocumento' => 'Descargar Documento',
        'imprimirDocumento' => 'Imprimir Documento',
        'guardarDocumento' => 'Guardar Documento',
        'limpiar' => 'Limpiar Formulario'
    ];
    
    foreach ($funciones_esperadas as $patron => $descripcion) {
        if (strpos($content, $patron) !== false) {
            echo "✅ <b>$descripcion</b> - OK<br>";
        } else {
            echo "⚠️ <b>$descripcion</b> - No encontrada<br>";
        }
    }
} else {
    echo "❌ Archivo <b>informes.php</b> no existe<br>";
}

// ====================
// 5. VERIFICAR INTERFAZ FRONTEND
// ====================
echo "<h2>5️⃣ INTERFAZ FRONTEND</h2>";

if (file_exists("informes.php")) {
    echo "✅ <b>informes.php</b> existe<br>";
    
    $content = file_get_contents("informes.php");
    
    $elementos = [
        'selectPlantilla' => 'Selector de Plantilla',
        'filtroSection' => 'Sección de Filtros',
        'editorSection' => 'Editor de Documento',
        'documento-editor' => 'Área de edición (textarea)'
    ];
    
    foreach ($elementos as $id => $descripcion) {
        if (strpos($content, $id) !== false) {
            echo "✅ Elemento: <code>$id</code> - $descripcion<br>";
        } else {
            echo "⚠️ Elemento: <code>$id</code> - No encontrado<br>";
        }
    }
} else {
    echo "❌ <b>informes.php</b> no existe<br>";
}

// ====================
// 6. VERIFICAR BOTONES DE ACCIÓN
// ====================
echo "<h2>6️⃣ BOTONES DE ACCIÓN</h2>";

try {
    $content = file_get_contents("informes.php");
    
    $acciones = [
        'btnDescargar' => 'Descargar PDF',
        'btnImprimir' => 'Imprimir',
        'btnGuardar' => 'Guardar Documento',
        'btnLimpiar' => 'Limpiar',
        'btnAdmin' => 'Administrar Plantillas'
    ];
    
    foreach ($acciones as $id => $descripcion) {
        if (strpos($content, $id) !== false) {
            echo "✅ Botón: <b>$descripcion</b> - Presente<br>";
        } else {
            echo "⚠️ Botón: <b>$descripcion</b> - No encontrado<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// ====================
// 7. VERIFICAR SESIÓN DEL USUARIO
// ====================
echo "<h2>7️⃣ VERIFICAR SESIÓN</h2>";

session_start();
if (isset($_SESSION['user_codigo'])) {
    echo "✅ <b>Usuario autenticado:</b> " . $_SESSION['user_email'] . "<br>";
    echo "   └─ Rol: " . $_SESSION['user_rol'] . "<br>";
} else {
    echo "⚠️ <b>No hay sesión activa</b><br>";
    echo "Solución: Accede primero a index.php y haz login<br>";
}

// ====================
// 8. TEST DE PERMISOS
// ====================
echo "<h2>8️⃣ TEST DE PERMISOS</h2>";

try {
    // Verificar si el usuario puede leer plantillas
    $test = $CONEXION->query("SELECT COUNT(*) as total FROM plantillas_maestro")->fetch(PDO::FETCH_ASSOC);
    echo "✅ <b>Lectura de plantillas permitida</b><br>";
    
    // Verificar tabla documentos  
    $test = $CONEXION->query("SHOW TABLES LIKE 'plantillas_documentos'");
    if ($test->rowCount() > 0) {
        echo "✅ <b>Tabla plantillas_documentos accesible</b><br>";
    }
} catch (Exception $e) {
    echo "❌ Error en permisos: " . $e->getMessage() . "<br>";
}

// ====================
// 9. FLUJO DE DATOS
// ====================
echo "<h2>9️⃣ FLUJO DE DATOS (Esperado)</h2>";
echo "<ol>";
echo "<li>Usuario selecciona plantilla → <b>cargarPlantilla()</b></li>";
echo "<li>Se cargan filtros dinámicos → AJAX a <b>/CargarFiltros</b></li>";
echo "<li>Usuario aplica filtros → AJAX a <b>/AplicarFiltro</b></li>";
echo "<li>Se carga documento en editor → <b>documento-editor</b> textarea</li>";
echo "<li>Usuario edita y acciona botón → <b>generarDocumento()</b></li>";
echo "<li>Documento se procesa y se puede descargar/imprimir/guardar</li>";
echo "</ol>";

// ====================
// 10. CHECKLIST FINAL
// ====================
echo "<h2>🔟 VERIFICACIÓN COMPLETA</h2>";
echo "<div style='background: #d4edda; padding: 10px; border-radius: 5px;'>";
echo "<b>✅ SI VES LA MAYORÍA DE ✅:</b><br>";
echo "🟢 El módulo Informes está funcional<br>";
echo "🟢 Accede a: <code>index.php</code> → Haz clic en <b>'Informes'</b> del menú<br>";
echo "🟢 Selecciona una plantilla y genera documentos<br>";
echo "🟢 Puedes descargar, imprimir o guardar documentos<br>";
echo "</div>";

?>
<hr>
<p style="font-size: 12px; color: #666;">
<b>Nota:</b> Este script es para diagnóstico. Puedes eliminarlo después de verificar que todo funciona.
</p>
