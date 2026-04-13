<?php
/**
 * Script para probar credenciales de BD
 * Accede a: cdipruebas.es/gespol/prueba_credenciales.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$usuario = 'gespol';
$bd = 'gespol';
$puerto = 3306;

// Lista de contraseñas a probar
$contrasenas_a_probar = [
    '',                    // Vacía
    'gespol',
    '1234',
    'password',
    'demo',
    'test',
    'admin',
    'gespol123',
    'gespol93',           // Añadida por sugerencia del usuario
    'cdipruebas',
    'cdipruebas123',
    'gespol@123',
    'Gespol123',
];

echo "<h2>🔍 Probando credenciales de BD...</h2>";
echo "<p>Host: <strong>$host</strong></p>";
echo "<p>Usuario: <strong>$usuario</strong></p>";
echo "<p>Puerto: <strong>$puerto</strong></p>";
echo "<p>BD: <strong>$bd</strong></p>";
echo "<hr>";

$encontrada = false;

foreach ($contrasenas_a_probar as $pass) {
    $display_pass = $pass === '' ? '(vacía)' : $pass;
    echo "Intentando con contraseña: <strong>$display_pass</strong>... ";
    
    try {
        $conn = @new mysqli($host, $usuario, $pass, $bd, $puerto);
        
        if ($conn->connect_error) {
            echo "❌ Fallo<br>";
        } else {
            echo "<span style='color: green; font-weight: bold;'>✅ ¡CONECTADO!</span><br>";
            
            // Mostrar info de la BD
            echo "<div style='background: #e8f5e9; padding: 15px; margin: 10px 0; border-left: 4px solid green;'>";
            echo "<strong>✅ CREDENCIALES CORRECTAS:</strong><br>";
            echo "Usuario: <strong>$usuario</strong><br>";
            echo "Contraseña: <strong>$display_pass</strong><br>";
            echo "Host: <strong>$host</strong><br>";
            echo "Puerto: <strong>$puerto</strong><br>";
            echo "BD: <strong>$bd</strong><br>";
            
            // Ver tablas
            $result = $conn->query("SHOW TABLES");
            echo "<br><strong>Tablas en la BD:</strong><br>";
            if ($result->num_rows > 0) {
                while($row = $result->fetch_array()) {
                    echo "  • " . $row[0] . "<br>";
                }
            } else {
                echo "  <em>(Base de datos vacía - Necesitas ejecutar sql.sql)</em><br>";
            }
            
            echo "</div>";
            
            $encontrada = true;
            $conn->close();
            break;
        }
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
}

if (!$encontrada) {
    echo "<div style='background: #ffebee; padding: 15px; border-left: 4px solid red;'>";
    echo "<strong style='color: red;'>❌ NO SE ENCONTRÓ COMBINACIÓN VÁLIDA</strong><br>";
    echo "Ninguna de las contraseñas probadas funciona.<br><br>";
    echo "<strong>Otros pasos:</strong><br>";
    echo "1. Ve a Plesk → Bases de datos → usuario 'gespol'<br>";
    echo "2. Haz clic en 'Cambiar contraseña' y genera una nueva<br>";
    echo "3. Copia la nueva contraseña<br>";
    echo "4. Edita este script y añade la contraseña a la lista<br>";
    echo "5. Vuelve a ejecutar este script<br>";
    echo "</div>";
}
?>
