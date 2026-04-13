<?php
/**
 * Script de diagnóstico v2: Verifica la estructura CORRECTA en Plesk
 * Sube esto a Plesk en /httpdocs/gespol/ y accede como:
 * https://cdipruebas.es/gespol/verificar_archivos_v2.php
 */

echo "<h1>🔍 DIAGNÓSTICO DE ARCHIVOS - ESTRUCTURA CORRECTA</h1>";
echo "<hr>";

function verificarArchivo($ruta, $nombre) {
    $rutaAbsoluta = __DIR__ . '/' . $ruta;
    if (file_exists($rutaAbsoluta)) {
        $size = filesize($rutaAbsoluta);
        echo "✅ <b>EXISTE:</b> $ruta <small>($size bytes)</small><br>";
        return true;
    } else {
        echo "❌ <b>FALTA:</b> $ruta<br>";
        return false;
    }
}

// Archivos CSS de DataTables (estructura correcta: libs/DataTables_OLD/)
echo "<h3>📋 ARCHIVOS CSS DATATABLES</h3>";
verificarArchivo('libs/DataTables_OLD/DataTables-1.10.22/css/jquery.dataTables.min.css', 'jQuery DataTables CSS');
verificarArchivo('libs/DataTables_OLD/Buttons-1.6.5/css/buttons.dataTables.min.css', 'Buttons CSS');

// Archivos JavaScript de DataTables (estructura correcta: libs/DataTables_OLD/)
echo "<h3>🔧 ARCHIVOS JAVASCRIPT DATATABLES</h3>";
verificarArchivo('libs/DataTables_OLD/DataTables-1.10.22/js/jquery.dataTables.min.js', 'jQuery DataTables JS');
verificarArchivo('libs/DataTables_OLD/Buttons-1.6.5/js/dataTables.buttons.min.js', 'Buttons JS');
verificarArchivo('libs/DataTables_OLD/Buttons-1.6.5/js/buttons.flash.min.js', 'Buttons Flash JS');
verificarArchivo('libs/DataTables_OLD/JSZip-2.5.0/jszip.min.js', 'JSZip JS');
verificarArchivo('libs/DataTables_OLD/pdfmake-0.1.36/pdfmake.min.js', 'PDFMake JS');
verificarArchivo('libs/DataTables_OLD/pdfmake-0.1.36/vfs_fonts.js', 'PDFMake VFS Fonts');
verificarArchivo('libs/DataTables_OLD/Buttons-1.6.5/js/buttons.print.min.js', 'Buttons Print JS');
verificarArchivo('libs/DataTables_OLD/Buttons-1.6.5/js/buttons.html5.min.js', 'Buttons HTML5 JS');

// Archivos de audio
echo "<h3>🔊 ARCHIVOS DE AUDIO</h3>";
verificarArchivo('sounds/notifica.mp3', 'Notificación MP3');
verificarArchivo('sounds/alerta.mp3', 'Alerta MP3');

// Verificar que index.php está actualizado
echo "<h3>📝 VERIFICAR INDEX.PHP</h3>";
$indexContent = file_get_contents(__DIR__ . '/index.php');
if (strpos($indexContent, 'libs/DataTables_OLD/') !== false) {
    echo "✅ <b>index.php</b> tiene las rutas CORRECTAS (libs/DataTables_OLD/)<br>";
} else {
    echo "❌ <b>index.php</b> aún tiene rutas antiguas<br>";
}

if (strpos($indexContent, 'sounds/notifica.mp3') !== false) {
    echo "✅ <b>index.php</b> referencia los archivos de audio<br>";
} else {
    echo "⚠️ <b>index.php</b> no tiene referencias a archivos de audio<br>";
}

echo "<hr>";
echo "<h3>📋 RESUMEN</h3>";
echo "<p><b>Si todos los archivos están marcados como ✅:</b></p>";
echo "<ol>";
echo "<li>Los 404 deben haber desaparecido</li>";
echo "<li>Haz clic en '<b>Usuarios</b>' del menú</li>";
echo "<li>La tabla DataTables debe cargar correctamente</li>";
echo "<li>No deben aparecer errores en la consola del navegador</li>";
echo "</ol>";
echo "<p><small><b>Nota:</b> Si aún ves errores, abre la consola del navegador (F12) y busca más detalles.</small></p>";
?>
