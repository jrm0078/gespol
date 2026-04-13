<?php
/**
 * EDITOR DE CONFIGURACIÓN - Edita config.inc.php desde el navegador
 * Accede a: cdipruebas.es/gespol/config_editor.php
 */

$config_file = 'inc/config.inc.php';
$mensaje = '';

if ($_POST) {
    $password = $_POST['password'] ?? '';
    
    // Intentar conectar PRIMERO
    $test_conn = @new mysqli('localhost', 'gespol', $password, 'gespol', 3306);
    
    if ($test_conn->connect_error) {
        $mensaje = '<div style="background: #ffebee; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #c62828;">';
        $mensaje .= '<strong>❌ Error de conexión</strong><br>';
        $mensaje .= 'La contraseña no es correcta o la BD no es accesible.<br>';
        $mensaje .= 'Error: ' . $test_conn->connect_error;
        $mensaje .= '</div>';
    } else {
        // Conexión OK - guardar
        $test_conn->close();
        
        $content = file_get_contents($config_file);
        $content = preg_replace(
            "/define\s*\(\s*'cdi_password'\s*,\s*'[^']*'\s*\)/",
            "define ('cdi_password','" . addslashes($password) . "')",
            $content
        );
        
        if (file_put_contents($config_file, $content)) {
            $mensaje = '<div style="background: #e8f5e9; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #2e7d32;">';
            $mensaje .= '<strong>✅ ¡Éxito!</strong> config.inc.php actualizado<br>';
            $mensaje .= '<a href="login.php" style="color: #1b5e20; font-weight: bold;">→ Ir al login</a>';
            $mensaje .= '</div>';
        } else {
            $mensaje = '<div style="background: #fff3e0; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #e65100;">';
            $mensaje .= '<strong>⚠️ Conexión OK pero no se pudo guardar</strong><br>';
            $mensaje .= 'Verifica permisos en inc/config.inc.php';
            $mensaje .= '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editor Config</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #f5f5f5; }
        .box { max-width: 450px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-top: 0; }
        .info { background: #e3f2fd; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; color: #1565c0; }
        label { display: block; font-weight: bold; margin-top: 15px; margin-bottom: 8px; color: #333; }
        input { width: 100%; padding: 12px; font-size: 16px; box-sizing: border-box; border: 2px solid #ddd; border-radius: 4px; }
        input:focus { outline: none; border-color: #2196f3; }
        button { background: #2196f3; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 20px; width: 100%; font-weight: bold; }
        button:hover { background: #1976d2; }
    </style>
</head>
<body>
<div class="box">
    <h2>⚙️ Configurar Contraseña BD</h2>
    
    <div class="info">
        <strong>Información:</strong><br>
        BD: <code>gespol</code><br>
        Usuario: <code>gespol</code><br>
        Host: <code>localhost:3306</code>
    </div>

    <?php echo $mensaje; ?>
    
    <form method="POST">
        <label>Contraseña del usuario 'gespol':</label>
        <p style="color: #999; font-size: 14px; margin: 0;">Obtén esta contraseña desde Plesk → Bases de datos → usuario gespol</p>
        <input type="text" name="password" placeholder="Ej: 1234" required autofocus>
        <button type="submit">🔐 Probar y Guardar</button>
    </form>
</div>
</body>
</html>
