<?php
session_start();
header('Content-Type: application/json');
if (isset($_SESSION['nombre'])) {
    echo json_encode([
        'nombre' => $_SESSION['nombre'],
        'foto' => isset($_SESSION['foto']) ? $_SESSION['foto'] : null
    ]);
} else {
    echo json_encode(['nombre' => 'Invitado']);
}
?>