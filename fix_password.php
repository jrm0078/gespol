<?php
// SCRIPT PARA ACTUALIZAR CONTRASEÑA A BCRYPT EN PLESK
// Ejecuta esta URL una sola vez y luego borralo: https://cdipruebas.es/gespol/fix_password.php

// Credenciales Plesk
$host = 'localhost';
$port = 3307;
$db = 'gespol';
$user = 'gespol';
$pass = 'gestion9393_';

// Hash bcrypt para contraseña "1234"
$bcrypt_hash = '$2y$10$5yNwkzQHROkMXuEZ3wpTyOYubrHwivSDrK6xFDn7cddsyonKI0W1m';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Actualizar contraseña
    $sql = "UPDATE usuario SET contrasenia = ? WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$bcrypt_hash, 'admin@admin.com']);
    
    echo "<h2 style='color:green'>✅ ÉXITO</h2>";
    echo "<p><strong>Contraseña actualizada:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> admin@admin.com</li>";
    echo "<li><strong>Contraseña:</strong> 1234</li>";
    echo "</ul>";
    echo "<p style='color:red'><strong>⚠️ IMPORTANTE:</strong> Borra este archivo (fix_password.php) del servidor por seguridad</p>";
    echo "<p><a href='https://cdipruebas.es/gespol/login.php'>→ Ir al login</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>❌ ERROR</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
