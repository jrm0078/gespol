<?php
/**
 * Script de diagnóstico: Verifica qué archivos existen en Plesk
 * Sube esto a Plesk en /httpdocs/gespol/ y accede como:
 * https://cdipruebas.es/gespol/verificar_archivos.php
 */

echo "<h2>🔍 DIAGNÓSTICO DE ARCHIVOS EN PLESK</h2>";
echo "<hr>";

// Función para verificar si un archivo existe
function verificarArchivo($ruta, $nombre) {
    $rutaAbsoluta = __DIR__ . '/' . $ruta;
    if (file_exists($rutaAbsoluta)) {
        echo "✅ <b>EXISTE:</b> $ruta<br>";
        return true;
    } else {
        echo "❌ <b>FALTA:</b> $ruta<br>";
        return false;
    }
}

// Archivos CSS de DataTables
echo "<h3>📋 ARCHIVOS CSS DATATABLES</h3>";
verificarArchivo('libs/DataTables/DataTables_OLD/DataTables-1.10.22/css/jquery.dataTables.min.css', 'jQuery DataTables CSS');
verificarArchivo('libs/DataTables/DataTables_OLD/Buttons-1.6.5/css/buttons.dataTables.min.css', 'Buttons CSS');

// Archivos JavaScript de DataTables
echo "<h3>🔧 ARCHIVOS JAVASCRIPT DATATABLES</h3>";
verificarArchivo('libs/DataTables/DataTables_OLD/DataTables-1.10.22/js/jquery.dataTables.min.js', 'jQuery DataTables JS');
verificarArchivo('libs/DataTables/DataTables_OLD/Buttons-1.6.5/js/dataTables.buttons.min.js', 'Buttons JS');
verificarArchivo('libs/DataTables/DataTables_OLD/Buttons-1.6.5/js/buttons.flash.min.js', 'Buttons Flash JS');
verificarArchivo('libs/DataTables/DataTables_OLD/JSZip-2.5.0/jszip.min.js', 'JSZip JS');
verificarArchivo('libs/DataTables/DataTables_OLD/pdfmake-0.1.36/pdfmake.min.js', 'PDFMake JS');
verificarArchivo('libs/DataTables/DataTables_OLD/pdfmake-0.1.36/vfs_fonts.js', 'PDFMake VFS Fonts');
verificarArchivo('libs/DataTables/DataTables_OLD/Buttons-1.6.5/js/buttons.print.min.js', 'Buttons Print JS');
verificarArchivo('libs/DataTables/DataTables_OLD/Buttons-1.6.5/js/buttons.html5.min.js', 'Buttons HTML5 JS');

// Archivos de audio
echo "<h3>🔊 ARCHIVOS DE AUDIO</h3>";
verificarArchivo('sounds/notifica.mp3', 'Notificación MP3');
verificarArchivo('sounds/alerta.mp3', 'Alerta MP3');

// Directorio libs completo
echo "<h3>📁 ESTRUCTURA DE DIRECTORIOS</h3>";
echo "<b>Contenido de /libs:</b><br>";
if (is_dir(__DIR__ . '/libs')) {
    $items = scandir(__DIR__ . '/libs');
    foreach ($items as $item) {
        if ($item != '.' && $item != '..') {
            $type = is_dir(__DIR__ . '/libs/' . $item) ? '📁' : '📄';
            echo "$type $item<br>";
        }
    }
} else {
    echo "❌ CARPETA /libs NO EXISTE<br>";
}

echo "<br><b>Contenido de /libs/DataTables:</b><br>";
if (is_dir(__DIR__ . '/libs/DataTables')) {
    $items = scandir(__DIR__ . '/libs/DataTables');
    foreach ($items as $item) {
        if ($item != '.' && $item != '..') {
            $type = is_dir(__DIR__ . '/libs/DataTables/' . $item) ? '📁' : '📄';
            echo "$type $item<br>";
        }
    }
} else {
    echo "❌ CARPETA /libs/DataTables NO EXISTE<br>";
}

echo "<br><b>Contenido de /libs/DataTables/DataTables_OLD (si existe):</b><br>";
if (is_dir(__DIR__ . '/libs/DataTables/DataTables_OLD')) {
    $items = scandir(__DIR__ . '/libs/DataTables/DataTables_OLD');
    foreach ($items as $item) {
        if ($item != '.' && $item != '..') {
            $type = is_dir(__DIR__ . '/libs/DataTables/DataTables_OLD/' . $item) ? '📁' : '📄';
            echo "$type $item<br>";
        }
    }
} else {
    echo "❌ CARPETA /libs/DataTables/DataTables_OLD NO EXISTE<br>";
}

echo "<br><b>Contenido de /sounds (si existe):</b><br>";
if (is_dir(__DIR__ . '/sounds')) {
    $items = scandir(__DIR__ . '/sounds');
    foreach ($items as $item) {
        if ($item != '.' && $item != '..') {
            echo "🔊 $item<br>";
        }
    }
} else {
    echo "❌ CARPETA /sounds NO EXISTE<br>";
}

echo "<hr>";
echo "<p><small>Sube este script a Plesk y accede a <code>https://cdipruebas.es/gespol/verificar_archivos.php</code> para ver qué falta.</small></p>";
?>
