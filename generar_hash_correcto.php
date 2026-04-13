<?php
/**
 * Generador de Hash Bcrypt
 * Accede a: cdipruebas.es/gespol/generar_hash_correcto.php
 */

if ($_POST['contraseña']) {
    $hash = password_hash($_POST['contraseña'], PASSWORD_BCRYPT, ['cost' => 10]);
    $sql = "UPDATE usuario SET contrasenia = '$hash' WHERE email = 'admin@admin.com';";
    $sql_insert = "INSERT INTO usuario(nombre,email,contrasenia,rol,activo) VALUES('admin','admin@admin.com','$hash','Superadmin',1) ON DUPLICATE KEY UPDATE contrasenia='$hash';";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Generador Hash</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #f5f5f5; }
        .box { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 8px; }
        h2 { color: #333; }
        input { width: 100%; padding: 10px; margin: 10px 0; font-size: 16px; box-sizing: border-box; }
        button { background: #2196f3; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; width: 100%; }
        button:hover { background: #1976d2; }
        .code { background: #f0f0f0; padding: 15px; border-radius: 4px; margin: 15px 0; overflow-x: auto; }
        code { font-family: monospace; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 4px; margin: 15px 0; }
    </style>
</head>
<body>
<div class="box">
    <h2>🔐 Generador de Hash Bcrypt</h2>
    
    <form method="POST">
        <label>Contraseña (ej: 1234):</label>
        <input type="text" name="contraseña" value="1234" required>
        <button type="submit">Generar Hash</button>
    </form>

    <?php if ($_POST['contraseña']): ?>
        <div class="info">
            <strong>✅ Hash generado:</strong>
            <div class="code">
                <code><?php echo $hash; ?></code>
            </div>

            <strong>📝 SQL para ejecutar en phpMyAdmin:</strong>
            <div class="code">
                <code style="word-break: break-all;"><?php echo $sql_insert; ?></code>
            </div>

            <p style="font-size: 12px; color: #666;">
                <strong>Instrucciones:</strong><br>
                1. Copia el SQL anterior<br>
                2. Ve a Plesk → phpMyAdmin<br>
                3. Pega el SQL en la pestaña SQL<br>
                4. Haz clic en Ejecutar<br>
                5. Intenta login con contraseña <strong>1234</strong>
            </p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
