<?php
/**
 * INSTALADOR - Crea tablas y configura la BD automáticamente
 * Accede a: cdipruebas.es/gespol/instalador.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$usuario = 'gespol';
$bd = 'gespol';
$puerto = 3306;

$contrasenas = ['', 'gespol', '1234', 'password', 'admin', 'gespol93', 'practicas93', 'gespol123', '123456', 'test'];

$con_exitosa = false;
$conn = null;
$pass_correcta = '';

// Intentar conectar
foreach ($contrasenas as $pass) {
    $conn = @new mysqli($host, $usuario, $pass, $bd, $puerto);
    if (!$conn->connect_error) {
        $con_exitosa = true;
        $pass_correcta = $pass;
        break;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Instalador GESPOL</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        .success { background: #e8f5e9; border-left: 4px solid #4caf50; padding: 15px; margin: 10px 0; }
        .error { background: #ffebee; border-left: 4px solid #f44336; padding: 15px; margin: 10px 0; }
        .info { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin: 10px 0; }
        h1 { color: #333; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
        pre { background: #f0f0f0; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>🚀 Instalador de GESPOL</h1>

    <?php if (!$con_exitosa): ?>
        <div class="error">
            <strong>❌ Error de conexión</strong>
            <p>No se pudo conectar a la base de datos con ninguna de las contraseñas probadas.</p>
            <p><strong>Contraseñas probadas:</strong></p>
            <ul>
                <?php foreach ($contrasenas as $p): ?>
                    <li><code><?php echo $p ?: '(vacía)'; ?></code></li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Solución:</strong> Ve a Plesk → Bases de datos → usuario "gespol" → Cambiar contraseña a "1234" → Recarga esta página</p>
        </div>
    <?php else: ?>
        <div class="success">
            <strong>✅ Conectado exitosamente</strong>
            <p>Contraseña correcta: <code><?php echo $pass_correcta ?: '(vacía)'; ?></code></p>
        </div>

        <?php
        // Ejecutar SQL para crear tablas
        $sql_queries = [
            "CREATE TABLE IF NOT EXISTS usuario (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(150),
                email VARCHAR(150) UNIQUE,
                contrasenia VARCHAR(255),
                rol ENUM('Superadmin','Admin','Usuario') DEFAULT 'Usuario',
                activo TINYINT(1) DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            "INSERT IGNORE INTO usuario(nombre,email,contrasenia,rol,activo)
            VALUES('admin','admin@admin.com','\$2y\$10\$Z/TIKfT0EJf/HKF7y8RivOPgfU3qnJHHVpI0eeJ4WQVdzfEpQ5Xm2','Superadmin',1)",
            
            "CREATE TABLE IF NOT EXISTS plantillas_maestro (
                cod_plantilla VARCHAR(50) PRIMARY KEY,
                nombre VARCHAR(255) NOT NULL,
                descripcion TEXT,
                tipo_documento VARCHAR(100),
                contenido LONGTEXT NOT NULL,
                tabla_origen VARCHAR(100),
                campo_clave VARCHAR(100),
                sql_consulta LONGTEXT,
                estado TINYINT(1) DEFAULT 1,
                fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
                fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            "CREATE TABLE IF NOT EXISTS plantillas_filtros (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cod_plantilla VARCHAR(50) NOT NULL,
                nombre_filtro VARCHAR(100),
                etiqueta VARCHAR(255),
                tipo_filtro VARCHAR(20),
                tabla_datos VARCHAR(100),
                campo_clave VARCHAR(100),
                campo_valor VARCHAR(100),
                sql_query LONGTEXT,
                operador VARCHAR(20),
                orden INT DEFAULT 999,
                requerido TINYINT(1) DEFAULT 0,
                activo TINYINT(1) DEFAULT 1,
                FOREIGN KEY (cod_plantilla) REFERENCES plantillas_maestro(cod_plantilla) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            "CREATE TABLE IF NOT EXISTS plantillas_variables (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cod_plantilla VARCHAR(50) NOT NULL,
                nombre_variable VARCHAR(100),
                etiqueta VARCHAR(255),
                tipo VARCHAR(50),
                requerido TINYINT(1) DEFAULT 0,
                orden INT DEFAULT 999,
                activo TINYINT(1) DEFAULT 1,
                FOREIGN KEY (cod_plantilla) REFERENCES plantillas_maestro(cod_plantilla) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            "CREATE TABLE IF NOT EXISTS plantillas_documentos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cod_plantilla VARCHAR(50) NOT NULL,
                id_usuario INT,
                contenido_final LONGTEXT,
                datos_json JSON,
                fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
                fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (cod_plantilla) REFERENCES plantillas_maestro(cod_plantilla) ON DELETE CASCADE,
                FOREIGN KEY (id_usuario) REFERENCES usuario(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            
            "INSERT IGNORE INTO plantillas_maestro (cod_plantilla, nombre, descripcion, tipo_documento, contenido, estado) VALUES
            ('presupuesto_1', 'Presupuesto Standard', 'Plantilla de presupuesto básico', 'PDF', '<h2>PRESUPUESTO</h2><p>Cliente: {%%cliente%%}</p><p>Monto: {%%monto%%}</p>', 1)",
            
            "INSERT IGNORE INTO plantillas_maestro (cod_plantilla, nombre, descripcion, tipo_documento, contenido, estado) VALUES
            ('contrato_1', 'Contrato Standard', 'Plantilla de contrato básico', 'PDF', '<h2>CONTRATO</h2><p>Fecha: {%%fecha%%}</p><p>Partes: {%%partes%%}</p>', 1)"
        ];

        $errores = [];
        foreach ($sql_queries as $i => $query) {
            if (!$conn->query($query)) {
                $errores[] = "Query " . ($i+1) . ": " . $conn->error;
            }
        }

        if (empty($errores)) {
            echo '<div class="success">';
            echo '<strong>✅ Instalación completada exitosamente!</strong>';
            echo '<p>Se han creado todas las tablas y el usuario admin.</p>';
            echo '</div>';
            
            echo '<div class="info">';
            echo '<strong>📝 Datos de acceso:</strong><br>';
            echo 'Email: <code>admin@admin.com</code><br>';
            echo 'Contraseña: <code>1234</code><br>';
            echo '<br>';
            echo '<strong>Ahora necesitas actualizar config.inc.php:</strong><br>';
            echo '<pre>';
            echo "define ('cdi_servername', 'localhost');\n";
            echo "define ('cdi_username','gespol');\n";
            echo "define ('cdi_password','" . $pass_correcta . "');\n";
            echo "define ('cdi_dbname','gespol');\n";
            echo '</pre>';
            echo '<strong>Accede a:</strong> <a href="https://cdipruebas.es/gespol/login.php">cdipruebas.es/gespol/login.php</a>';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<strong>❌ Errores ejecutando SQL:</strong>';
            echo '<ul>';
            foreach ($errores as $err) {
                echo '<li>' . $err . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        $conn->close();
    ?>
    <?php endif; ?>
</div>
</body>
</html>
