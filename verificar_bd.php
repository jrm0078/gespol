<?php
/**
 * SCRIPT DE VERIFICACIÓN - Debugging de BD y Login
 * Accede a: cdipruebas.es/verificar_bd.php
 * 
 * ⚠️ ELIMINAR DESPUÉS DE VERIFICAR (es un archivo de desarrollo/debug)
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir config para obtener credenciales correctas
require_once 'inc/config.inc.php';

// Conexión BD usando credenciales de config
$servername = cdi_servername;
$username = cdi_username;
$password = cdi_password;
$dbname = cdi_dbname;
$port = 3306;  // Puerto correcto de Plesk

try {
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    
    if ($conn->connect_error) {
        die("❌ Error de conexión: " . $conn->connect_error);
    }
    
    echo "<h2>✅ Conexión a BD OK</h2>";
    
    // 1. Verificar tablas
    echo "<h3>1. TABLAS EXISTENTES:</h3>";
    $result = $conn->query("SHOW TABLES");
    while($row = $result->fetch_array()) {
        echo "  ✅ " . $row[0] . "<br>";
    }
    
    // 2. Verificar tabla usuario
    echo "<h3>2. TABLA 'usuario':</h3>";
    $result = $conn->query("SELECT COUNT(*) as total FROM usuario");
    $row = $result->fetch_assoc();
    echo "  Total usuarios: " . $row['total'] . "<br>";
    
    // 3. Listar usuarios
    echo "<h3>3. USUARIOS EN BD:</h3>";
    $result = $conn->query("SELECT id, nombre, email, rol FROM usuario");
    while($row = $result->fetch_assoc()) {
        echo "  👤 Email: " . $row['email'] . " | Rol: " . $row['rol'] . "<br>";
    }
    
    // 4. Verificar contraseña admin
    echo "<h3>4. VERIFICAR CONTRASEÑA:</h3>";
    $result = $conn->query("SELECT id, email, contrasenia FROM usuario WHERE email = 'admin@admin.com'");
    $user = $result->fetch_assoc();
    
    if($user) {
        echo "  Email encontrado: " . $user['email'] . "<br>";
        echo "  ID: " . $user['id'] . "<br>";
        
        // Probar password_verify
        if(password_verify('admin', $user['contrasenia'])) {
            echo "  ✅ Contraseña 'admin' es CORRECTA<br>";
        } else {
            echo "  ❌ Contraseña 'admin' es INCORRECTA<br>";
            echo "  Hash en BD: " . $user['contrasenia'] . "<br>";
        }
    } else {
        echo "  ❌ Usuario admin@admin.com NO EXISTE<br>";
        echo "  → Necesitas ejecutar el SQL.sql<br>";
    }
    
    // 5. Verificar tablas plantillas
    echo "<h3>5. TABLAS PLANTILLAS:</h3>";
    $plantillasTablas = ['plantillas_maestro', 'plantillas_filtros', 'plantillas_variables', 'plantillas_documentos'];
    foreach($plantillasTablas as $tabla) {
        $result = $conn->query("SHOW TABLES LIKE '$tabla'");
        if($result->num_rows > 0) {
            echo "  ✅ " . $tabla . "<br>";
        } else {
            echo "  ❌ " . $tabla . " (NO EXISTE)<br>";
        }
    }
    
    $conn->close();
    
} catch(Exception $e) {
    die("<h2>❌ Error: " . $e->getMessage() . "</h2>");
}

?>
<hr>
<p><strong>⚠️ INSTRUCCIONES:</strong></p>
<ul>
    <li>Si ves ❌ en tablas → Ejecuta <code>sql.sql</code> en phpMyAdmin</li>
    <li>Si contraseña es incorrecta → Hay problema con el hash</li>
    <li>Si usuario NO EXISTE → Falta ejecutar el SQL</li>
    <li>ELIMINA este archivo después de verificar</li>
</ul>
