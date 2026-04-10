<?php
header('Content-Type: application/json; charset=utf-8');

// Log de la petición
$log = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'uri' => $_SERVER['REQUEST_URI'],
    'action' => isset($_GET['action']) ? $_GET['action'] : 'NO ACTION',
    'post' => array_keys($_POST),
    'timestamp' => date('Y-m-d H:i:s')
];

// Escribir en un archivo de log
file_put_contents('/tmp/ajax_requests.log', json_encode($log) . PHP_EOL, FILE_APPEND);

echo json_encode([
    'status' => 'ok',
    'message' => 'AJAX funciona',
    'log' => $log
]);
?>
