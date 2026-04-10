<?php
// MANEJADOR DE LOGIN SIMPLE - SIN SLIM FRAMEWORK

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");

include 'config.inc.php';
include 'genericasPHP.php';

// Detectar si es POST al archivo login.php (sin importar la acción específica)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $db = getConnection();
        $sql = "SELECT id, nombre, email, contrasenia, rol FROM usuario WHERE email = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        
        if ($usuario && password_verify($password, $usuario->contrasenia)) {
            session_start();
            $_SESSION["validacion"] = "ok";
            $_SESSION["user_codigo"] = $usuario->id;
            $_SESSION["user_descripcion"] = $usuario->nombre;
            $_SESSION["user_email"] = $usuario->email;
            $_SESSION["user_rol"] = $usuario->rol;
            
            echo json_encode(["validacion" => "ok"]);
        } else {
            echo json_encode(["validacion" => "error", "error" => "Credenciales inválidas"]);
        }
    } catch (Exception $e) {
        echo json_encode(["validacion" => "error", "error" => $e->getMessage()]);
    }
    exit;
}

// Si no es login, devolver error
echo json_encode(["validacion" => "error", "error" => "Acción no válida"]);
?>
