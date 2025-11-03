<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (isset($_POST['darkMode'])) {
    // Guardar preferencia en sesión
    $_SESSION['darkMode'] = filter_var($_POST['darkMode'], FILTER_VALIDATE_BOOLEAN);
    
    // Devolver respuesta exitosa
    echo json_encode([
        'success' => true,
        'darkMode' => $_SESSION['darkMode']
    ]);
} else {
    // Error si falta el parámetro
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Falta el parámetro darkMode'
    ]);
}
?>