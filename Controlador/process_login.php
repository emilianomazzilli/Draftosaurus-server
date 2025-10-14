<?php
require_once __DIR__ . '/../Modelo/conexion.php'; // carga $con y session

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Metodo no permitido.';
    exit;
}

$usuarioOrEmail = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$contrasena     = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

if ($usuarioOrEmail === '' || $contrasena === '') {
    echo 'Completa usuario y contraseña.';
    exit;
}

// Buscar por usuario o email en la tabla `usuario`
$stmt = $con->prepare('SELECT id, nombre, usuario, contrasenia FROM `usuario` WHERE usuario = ? OR email = ? LIMIT 1');
if (!$stmt) {
    echo 'Error interno.';
    exit;
}
$stmt->bind_param('ss', $usuarioOrEmail, $usuarioOrEmail);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo 'Usuario o email no encontrado.';
    exit;
}

// Verificar contraseÃ±a (soporta password_hash o fallback a texto plano)
$stored = $user['contrasenia'];
$login_ok = false;
if (password_verify($contrasena, $stored)) {
    $login_ok = true;
} elseif ($contrasena === $stored) {
    $login_ok = true;
}

if (!$login_ok) {
    echo 'Contraseña incorrecta.';
    exit;
}

// Éxito: guardar en sesión
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}
$_SESSION['user_id'] = $user['id'];
$_SESSION['usuario'] = $user['usuario'];
$_SESSION['nombre']  = $user['nombre'];

$con->close();

echo 'OK';
exit;
