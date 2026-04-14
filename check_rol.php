<?php
include 'inc/config.inc.php';
include 'inc/genericasPHP.php';

header('Content-Type: text/plain');

try {
    $pdo = getConnection();
    $rows = $pdo->query('SELECT id, nombre, email, rol FROM usuario')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "id={$r['id']} | nombre={$r['nombre']} | email={$r['email']} | rol=[{$r['rol']}]\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
