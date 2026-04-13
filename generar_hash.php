<?php
/**
 * Generador de Hash Bcrypt
 * Accede a: cdipruebas.es/generar_hash.php
 * 
 * Copia el hash que aparece aquí y actualiza la BD
 */

$password = "1234";
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

echo "<h2>Hash Bcrypt para contraseña: <strong>1234</strong></h2>";
echo "<p><code>$hash</code></p>";
echo "<p><strong>Copia este hash ↑ y reemplaza en la BD</strong></p>";
echo "<hr>";
echo "<h3>SQL para actualizar:</h3>";
echo "<pre>UPDATE usuario SET contrasenia = '$hash' WHERE email = 'admin@admin.com';</pre>";
echo "<p>Ejecuta esto en phpMyAdmin de Plesk</p>";

// Verificar
echo "<h3>Verificación:</h3>";
if(password_verify("1234", $hash)) {
    echo "✅ El hash es correcto para la contraseña '1234'";
} else {
    echo "❌ Algo salió mal";
}

?>
