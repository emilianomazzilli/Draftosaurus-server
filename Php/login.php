<?php
// login.php (form) - muestra errores pasados por GET
session_start();
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link rel="stylesheet" href="Css/styles.css">
</head>
<body>
  <main class="container">
    <?php if ($error): ?>
      <div style="background:#fdd;border:1px solid #f99;padding:10px;border-radius:6px;margin:12px auto;max-width:360px;color:#900;text-align:center;">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <form class="formulario" method="post" action="process_login.php">
      <label for="username">Usuario</label>
      <input id="username" name="username" type="text" required>

      <label for="password">Contraseña</label>
      <input id="password" name="password" type="password" required>

      <button class="btn aceptar" type="submit">Entrar</button>
    </form>
  </main>
</body>
</html>