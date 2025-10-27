<?php
// Controlador/agregar.php
declare(strict_types=1);

require_once __DIR__ . '/../Modelo/conexion.php'; // Debe definir $con (mysqli)

// Detectar si es AJAX (envío con X-Requested-With: XMLHttpRequest)
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
  && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Si no es POST, mostrar el formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
?>
  <!DOCTYPE html>
  <html lang="es">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Draftosaurus</title>
    <link rel="stylesheet" href="../Css/styles.css">
    <link rel="stylesheet" href="../Css/form.css">
    <link rel="stylesheet" href="../Css/media.css">
  </head>

  <body>
    <div class="borde top"></div>
    <div class="borde bottom"></div>
    <div class="borde left"></div>
    <div class="borde right"></div>

    <div class="container">
      <h1 class="titulo">Registrarse</h1>

      <form class="formulario" action="../Controlador/agregar.php" method="post" novalidate>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required autocomplete="email">

        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" required autocomplete="username">

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="contrasena" required autocomplete="new-password">

        <div class="strength-bar" id="strengthBar"></div>
        <span id="strengthText"></span>

        <ul class="password-require">
          <li>Al menos 8 caracteres</li>
          <li>Al menos una letra mayúscula</li>
          <li>Al menos una letra minúscula</li>
          <li>Al menos un número</li>
          <li>Al menos un carácter especial (!@#$%^&*)</li>
        </ul>

        <button type="submit" class="btn aceptar">Aceptar</button>
      </form>

      <div style="text-align: center; margin-top: 15px;">
        <a href="../Vista/login.php" style="color: #000000ff;">¿Ya tenés una cuenta? Iniciá sesión</a>
      </div>
    </div>

    <script src="../js/script.js"></script>
  </body>

  </html>
<?php
  exit;
}

// -------- Procesar registro (POST) --------
$email      = trim($_POST['email']      ?? '');
$usuario    = trim($_POST['usuario']    ?? '');
$contrasena =        $_POST['contrasena'] ?? '';

// Validaciones básicas
if ($email === '' || $usuario === '' || $contrasena === '') {
  $msg = 'Por favor, completá todos los campos.';
  if ($isAjax) {
    echo $msg;
    exit;
  }
  header('Location: ../Controlador/agregar.php?error=empty');
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $msg = 'Email no válido.';
  if ($isAjax) {
    echo $msg;
    exit;
  }
  header('Location: ../Controlador/agregar.php?error=email');
  exit;
}

// Reglas mínimas (alineadas con la barra de fuerza)
$weak = (
  strlen($contrasena) < 8 ||
  !preg_match('/[A-Z]/',        $contrasena) ||
  !preg_match('/[a-z]/',        $contrasena) ||
  !preg_match('/\d/',           $contrasena) ||
  !preg_match('/[^A-Za-z0-9]/', $contrasena)
);

if ($weak) {
  $msg = 'La contraseña no cumple los requisitos.';
  if ($isAjax) {
    echo $msg;
    exit;
  }
  header('Location: ../Controlador/agregar.php?error=weak');
  exit;
}

// ¿Usuario o email ya existen?
$check = $con->prepare('SELECT id FROM usuario WHERE usuario = ? OR email = ? LIMIT 1');
if (!$check) {
  $msg = 'Error interno.';
  if ($isAjax) {
    echo $msg;
    exit;
  }
  header('Location: ../Controlador/agregar.php?error=internal');
  exit;
}
$check->bind_param('ss', $usuario, $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
  $check->close();
  $msg = 'Usuario o email ya registrado.';
  if ($isAjax) {
    echo $msg;
    exit;
  }
  header('Location: ../Controlador/agregar.php?error=exists');
  exit;
}
$check->close();

// Insertar
$hash = password_hash($contrasena, PASSWORD_DEFAULT);
$stmt = $con->prepare('INSERT INTO usuario (email, usuario, contrasenia) VALUES (?, ?, ?)');
if (!$stmt) {
  $msg = 'Error interno.';
  if ($isAjax) {
    echo $msg;
    exit;
  }
  header('Location: ../Controlador/agregar.php?error=internal');
  exit;
}
$stmt->bind_param('sss', $email, $usuario, $hash);

if ($stmt->execute()) {
  $stmt->close();
  $con->close();

  if ($isAjax) {
    echo 'OK';
    exit;
  } // Respuesta ideal para fetch/XHR
  header('Location: ../Vista/login.php?ok=1');
  exit;
} else {
  $stmt->close();
  $con->close();
  $msg = 'No se pudo registrar. Intentá nuevamente.';
  if ($isAjax) {
    echo $msg;
    exit;
  }
  header('Location: ../Controlador/agregar.php?error=fail');
  exit;
}
