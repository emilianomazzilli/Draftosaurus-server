<?php
require_once __DIR__ . '/conexion.php'; // carga $con y session

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Html/login.html');
    exit;
}

$usuarioOrEmail = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$contrasena     = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

if ($usuarioOrEmail === '' || $contrasena === '') {
    header('Location: ../Html/login.html?error=' . urlencode('Completa usuario y contraseña.'));
    exit;
}

// Buscar por usuario o email en la tabla `auto`
$stmt = $con->prepare('SELECT id, nombre, usuario, contrasenia FROM `auto` WHERE usuario = ? OR email = ? LIMIT 1');
if (!$stmt) {
    header('Location: ../Html/login.html?error=' . urlencode('Error interno.'));
    exit;
}
$stmt->bind_param('ss', $usuarioOrEmail, $usuarioOrEmail);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header('Location: ../Html/login.html?error=' . urlencode('Usuario o email no encontrado.'));
    exit;
}

// Verificar contraseña (soporta password_hash o fallback a texto plano)
$stored = $user['contrasenia'];
$login_ok = false;
if (password_verify($contrasena, $stored)) {
    $login_ok = true;
} elseif ($contrasena === $stored) {
    $login_ok = true;
}

if (!$login_ok) {
    header('Location: ../Html/login.html?error=' . urlencode('Contraseña incorrecta.'));
    exit;
}

// Éxito: guardar en sesión y redirigir al menú
$_SESSION['user_id'] = $user['id'];
$_SESSION['usuario'] = $user['usuario'];
$_SESSION['nombre']  = $user['nombre'];

$con->close();

header('Location: ../Html/menu.html');
exit;
?>