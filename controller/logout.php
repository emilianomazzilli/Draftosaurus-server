<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}

// destruir sesión
$_SESSION = [];
session_unset();
session_destroy();

// redirigir al inicio
header("Location: ../Vista/index.php");
exit;
