<?php
// Cierre de sesión sin dependencias externas
session_start();
session_unset();
session_destroy();

// Redirigir al login (ruta relativa desde inc/)
header('Location: ../login.php');
exit;
?>
