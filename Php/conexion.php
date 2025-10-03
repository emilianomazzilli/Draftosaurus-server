<?php
// Configuración de conexión (XAMPP: root sin contraseña por defecto)
$servidor = "localhost";
$usr      = "root";
$pwd      = "";
$bd       = "login";

// Mostrar errores de mysqli en desarrollo (opcional)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$con = new mysqli($servidor, $usr, $pwd, $bd);
$con->set_charset('utf8mb4');

if ($con->connect_errno) {
    error_log("La conexión no se pudo establecer: " . $con->connect_error);
    // Mensaje genérico al usuario (no imprimir datos sensibles)
    die("Error de conexión a la base de datos.");
}

?>