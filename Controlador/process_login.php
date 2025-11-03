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

// Buscar por usuario o email (traer también foto y email)
$stmt = $con->prepare('SELECT id, usuario, contrasenia, foto, email FROM `usuario` WHERE usuario = ? OR email = ? LIMIT 1');
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

// Verificar contraseña (hash o texto plano legacy)
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
session_regenerate_id(true);

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['usuario'] = $user['usuario'];          // ← clave para get_user.php
$_SESSION['nombre']  = $user['usuario'];          // útil para HTML inicial
$_SESSION['foto']    = $user['foto'] ?? null;     // si existe en BD
$_SESSION['email']   = $user['email'] ?? null;    // nuevo: email en sesión

$con->close();

echo 'OK';
exit;
