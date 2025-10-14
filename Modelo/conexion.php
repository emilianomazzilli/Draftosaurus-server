<?php
// conexion.php - NO debe haber NADA antes de esta línea
// Configuración de conexión
$servidor = "localhost";
$usr      = "root";
$pwd      = "";
$bd       = "draftosaurus_db";

// Mostrar errores de mysqli en desarrollo
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Crear conexión
$con = new mysqli($servidor, $usr, $pwd, $bd);
$con->set_charset('utf8mb4');

// Verificar conexión
if ($con->connect_errno) {
    error_log("Error de conexión: " . $con->connect_error);
    die("Error de conexión a la base de datos.");
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}
