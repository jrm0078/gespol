<?php
/**
 * Índice principal de GESPOL
 * Redirige al login si no hay sesión, o al dashboard si existe
 */

@session_start();

if(isset($_SESSION["validacion"]) && $_SESSION["validacion"] == "ok") {
    // Si hay sesión activa, carga el dashboard
    header("Location: /gespol/index.php");
} else {
    // Si no hay sesión, redirige al login
    header("Location: /gespol/login.php");
}

exit();

?>
