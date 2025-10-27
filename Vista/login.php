<?php
// login.php - Vista de inicio de sesión
if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
  ]);
  session_start();
}

// Si ya está logueado, redirigir al menú
if (isset($_SESSION['user_id'])) {
  header('Location: ../Vista/menu.php');
  exit;
}

// Obtener mensaje de error si existe
$error = $_GET['error'] ?? '';
$success = $_GET['ok'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión - Draftosaurus</title>
  <link rel="stylesheet" href="../Css/styles.css">
  <link rel="stylesheet" href="../Css/form.css">
  <link href="https://fonts.googleapis.com/css2?family=Agbalumo&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../Css/media.css">
</head>

<body>
  <div class="borde top"></div>
  <div class="borde bottom"></div>
  <div class="borde left"></div>
  <div class="borde right"></div>
  <div class="container">
    <h1 class="titulo">Iniciar sesión</h1>

    <?php if ($success): ?>
      <div class="success-message">
        ¡Registro exitoso! Ahora puedes iniciar sesión.
      </div>
    <?php endif; ?>

    <form class="formulario" id="login-form" action="../Controlador/process_login.php" method="post">
      <label for="usuario">Usuario o Email</label>
      <input type="text" id="usuario" name="usuario">

      <label for="password">Contraseña</label>
      <input type="password" id="password" name="contrasena">

      <?php if ($error): ?>
        <div class="error-message">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>


      <button type="submit" class="btn aceptar">Aceptar</button>
    </form>

    <div class="text-links">
      <a class="text-link" href="index.php">Volver al inicio</a>
    </div>

    <div class="text-links">
      <a class="text-link" href="../Controlador/agregar.php">¿Aún no tenés cuenta?, Registrate</a>
    </div>
  </div>
   <!-- Dinosaruio Abajo a La Derecha todo pro -->
  <div class="overlay-img">
    <img src="../img/dino.png">
  </div>

  <script src="../js/script.js"></script>
</body>

</html>