<?php
include __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// Campos (acepta 'contrasenia' o 'contrasena')
$nombre     = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$email      = isset($_POST['email']) ? trim($_POST['email']) : '';
$usuario    = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$contrasena = isset($_POST['contrasenia']) ? $_POST['contrasenia'] : (isset($_POST['contrasena']) ? $_POST['contrasena'] : '');

if ($nombre === '' || $email === '' || $usuario === '' || $contrasena === '') {
    http_response_code(400);
    exit('Faltan datos requeridos.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Email no válido.');
}

// Comprobar si usuario o email ya existen
$check = $con->prepare("SELECT id FROM `auto` WHERE usuario = ? OR email = ? LIMIT 1");
if (!$check) {
    http_response_code(500);
    exit('Error interno.');
}
$check->bind_param('ss', $usuario, $email);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    $check->close();
    http_response_code(409);
    exit('Usuario o email ya registrado.');
}
$check->close();

// Insertar usuario con contraseña hasheada
$hash = password_hash($contrasena, PASSWORD_DEFAULT);
$stmt = $con->prepare("INSERT INTO `auto` (`nombre`, `email`, `usuario`, `contrasenia`) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    http_response_code(500);
    exit('Error interno.');
}
$stmt->bind_param('ssss', $nombre, $email, $usuario, $hash);
if ($stmt->execute()) {
    $stmt->close();
    $con->close();
    // Ruta absoluta para XAMPP (ajusta "Gonzadrafto" si pones otra carpeta)
    header('Location: /Gonzadrafto/Html/login.html');
    exit;
} else {
    $stmt->close();
    $con->close();
    http_response_code(500);
    exit('Error al crear registro.');
}
?>