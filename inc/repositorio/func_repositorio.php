<?php
/**
 * func_repositorio.php
 * Funciones CRUD para el módulo Repositorio
 */

// Extensiones permitidas (NO ejecutables)
define('REPO_EXT_PERMITIDAS', [
    'jpg','jpeg','png','gif','webp','svg','bmp','ico',
    'pdf','doc','docx','xls','xlsx','ppt','pptx','odt','ods','odp',
    'txt','csv','xml','json',
    'zip','rar','7z','tar','gz',
    'mp4','mp3','avi','mov','webm','ogg',
]);

// Extensiones bloqueadas explícitamente (ejecutables / peligrosas)
define('REPO_EXT_BLOQUEADAS', [
    'php','php3','php4','php5','php7','phtml','phar',
    'asp','aspx','jsp','jspx','cfm',
    'js','ts','mjs','cjs',
    'exe','bat','cmd','com','scr','vbs','vbe','wsf','wsh','ps1','psm1',
    'sh','bash','zsh','csh','ksh','fish',
    'py','rb','pl','pm','cgi','lua','go',
    'htaccess','htpasswd',
    'dll','so','dylib',
    'jar','war','ear','class',
    'swf','fla',
]);

define('REPO_BASE_PATH', __DIR__ . '/../../repositorio/');
define('REPO_BASE_URL',  'repositorio/');
define('REPO_MAX_SIZE',  20 * 1024 * 1024); // 20 MB

/**
 * Valida y sube un fichero al repositorio.
 * @return array ['ok'=>bool, 'error'=>string|null, 'nombre_fichero'=>string, 'tipo'=>string, 'tamano'=>int]
 */
function SubirFicheroRepositorio($fileInput, $directorio = '') {
    if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] !== UPLOAD_ERR_OK) {
        $codigo = $_FILES[$fileInput]['error'] ?? -1;
        return ['ok' => false, 'error' => "Error al recibir el fichero (código $codigo)"];
    }

    $file     = $_FILES[$fileInput];
    $nombreOrig = basename($file['name']);
    $ext      = strtolower(pathinfo($nombreOrig, PATHINFO_EXTENSION));

    // Bloquear extensiones peligrosas
    if (in_array($ext, REPO_EXT_BLOQUEADAS)) {
        return ['ok' => false, 'error' => "Tipo de fichero no permitido: .$ext"];
    }
    // Solo permitir extensiones en lista blanca
    if (!in_array($ext, REPO_EXT_PERMITIDAS)) {
        return ['ok' => false, 'error' => "Tipo de fichero no permitido: .$ext"];
    }
    // Validar tamaño
    if ($file['size'] > REPO_MAX_SIZE) {
        return ['ok' => false, 'error' => 'El fichero supera el tamaño máximo permitido (20 MB)'];
    }
    // Validar MIME real del fichero (no el reportado por el cliente)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    // Bloquear si el MIME es de tipo ejecutable aunque cambie la extensión
    $mimesEjecutablesBloquear = ['application/x-php','text/x-php','application/x-httpd-php',
        'application/x-sh','text/x-shellscript','application/x-executable',
        'application/x-msdos-program','application/x-msdownload'];
    if (in_array($mime, $mimesEjecutablesBloquear)) {
        return ['ok' => false, 'error' => 'Tipo de fichero no permitido por seguridad'];
    }

    // Preparar directorio destino
    $directorio = trim($directorio, '/\\ ');
    // Sanitizar subdirectorio: solo letras, números, guiones, guiones bajos, slashes
    $directorio = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $directorio);
    // Evitar path traversal
    $directorio = str_replace(['..', './'], '', $directorio);

    $destDir = REPO_BASE_PATH . ($directorio !== '' ? $directorio . '/' : '');
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
        // .htaccess para bloquear ejecución PHP en esta carpeta
        file_put_contents($destDir . '.htaccess', "Options -ExecCGI -Indexes\n<FilesMatch \"\\.ph(p[2-9]?|tml|ar)$\">\n  Require all denied\n</FilesMatch>\n");
    }

    // Generar nombre único para evitar sobreescrituras
    $nombreFichero = uniqid('repo_', true) . '.' . $ext;
    $destPath      = $destDir . $nombreFichero;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        $phpErr = error_get_last();
        $detalle = $phpErr ? $phpErr['message'] : 'sin detalle';
        return ['ok' => false, 'error' => "No se pudo guardar el fichero. Ruta: $destPath | PHP: $detalle | is_dir: " . (is_dir($destDir)?'si':'no') . " | tmp: " . $file['tmp_name']];
    }

    return [
        'ok'              => true,
        'error'           => null,
        'nombre_original' => $nombreOrig,
        'nombre_fichero'  => $nombreFichero,
        'tipo'            => $mime,
        'tamano'          => $file['size'],
    ];
}

/**
 * Lista todos los subdirectorios existentes bajo REPO_BASE_PATH
 */
function ListarDirectoriosRepositorio() {
    $dirs = [''];  // raíz siempre disponible
    if (!is_dir(REPO_BASE_PATH)) return $dirs;

    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(REPO_BASE_PATH, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($it as $file) {
        if ($file->isDir()) {
            $rel = str_replace(REPO_BASE_PATH, '', $file->getPathname());
            $rel = str_replace('\\', '/', $rel);
            if (strpos($rel, '.') === false) { // Ocultar .htaccess etc
                $dirs[] = $rel;
            }
        }
    }
    sort($dirs);
    return $dirs;
}

/**
 * Obtiene un registro de repositorio por ID
 */
function ObtenerRepositorio($id) {
    try {
        $db  = getConnection();
        $stmt = $db->prepare("SELECT * FROM repositorio WHERE id = :id");
        $stmt->execute([':id' => intval($id)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Elimina un registro y su fichero del servidor
 */
function EliminarRepositorio($id) {
    try {
        $registro = ObtenerRepositorio($id);
        if (!$registro) return ['ok' => false, 'error' => 'Registro no encontrado'];

        // Eliminar fichero físico
        $directorio = trim($registro['directorio'], '/\\ ');
        $rutaFichero = REPO_BASE_PATH . ($directorio !== '' ? $directorio . '/' : '') . $registro['nombre_fichero'];
        if (is_file($rutaFichero)) {
            unlink($rutaFichero);
        }

        $db = getConnection();
        $stmt = $db->prepare("DELETE FROM repositorio WHERE id = :id");
        $stmt->execute([':id' => intval($id)]);
        return ['ok' => true];
    } catch (Exception $e) {
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}
