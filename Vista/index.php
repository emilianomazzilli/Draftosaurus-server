<?php require __DIR__ . '/../lang/boot.php'; ?>
<?php
// index.php
if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
  ]);
  session_start();
}

// Si ya está logueado, redirigir al menú
if (!empty($_SESSION['user_id'])) {
  header("Location: ../Vista/menu.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Draftosaurus</title>
  <link rel="stylesheet" href="../Css/styles.css">
  <link rel="stylesheet" href="../Css/index.css">
  <link rel="stylesheet" href="../Css/menu.css">
  <link rel="stylesheet" href="../Css/media.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Agbalumo&display=swap" rel="stylesheet">
</head>

<body>
  <!-- Botón de opciones -->
  <button id="opciones-btn" class="opciones-btn" title="<?= t('menu.options') ?>">
    <img src="../img/engranaje.png" alt="<?= t('menu.options') ?>" class="opciones-icon">
  </button>
  <!-- Overlay de opciones -->
  <div id="opciones-overlay" class="opciones-overlay" style="display:none;">
    <div class="opciones-modal-wrapper">
    </div>
    <div class="opciones-modal">
      <span class="opciones-nombre-titulo">Opciones</span>
      <span class="close-opciones" onclick="cerrarOpcionesOverlay()">×</span>
      <div class="opciones-contenido">
        <div class="opcion-control">
          <label for="sonido-range"><b>Sonido</b></label>
          <input type="range" id="sonido-range" min="0" max="100" value="50" oninput="actualizarSonido(this.value)">
          <span id="sonido-valor">50</span>%
        </div>
        <div class="opcion-control">
          <label for="musica-range"><b>Música</b></label>
          <input type="range" id="musica-range" min="0" max="100" value="50" oninput="actualizarMusica(this.value)">
          <span id="musica-valor">50</span>%
        </div>

        <div class="opcion-item">
          <span>Modo Oscuro</span>
          <label class="theme-switch">
            <input type="checkbox" id="darkMode">
            <span class="slider"></span>
          </label>
        </div>
        <!-- Selector de idioma -->
        <div class="banderas">
          <a href="?lang=en" title="English">
            <img class="flag-icon" src="../img/ingles.png" alt="English">
          </a>
          <a href="?lang=es" title="Español">
            <img class="flag-icon" src="../img/español.png" alt="Español">
          </a>
        </div>
      </div>
    </div>
  </div>
  <!-- Dinosaruio Abajo a La Derecha todo pro -->
  <div class="overlay-img">
    <img src="../img/dino.png">
  </div>
  <!-- elpepe -->
  <div class="merch">
    <a href="https://merch-gensoftware.netlify.app/" target="_blank">
      <img src="../img/shopping.png" alt="Merch GEN Software">
    </a>
  </div>
  <!-- Los Bordes de la Pantalla -->
  <div class="borde top"></div>
  <div class="borde bottom"></div>
  <div class="borde left"></div>
  <div class="borde right"></div>
  <div class="container">
    <!-- Logo -->
    <img src="../img/by_bigger.png" alt="Draftosaurus Logo" class="logo-draftosaurus" loading="lazy">
    <div class="bottom-buttons">
      <a href="../Vista/login.php" class="btn"><?= t('index.login') ?></a>
      <a href="../Controlador/agregar.php" class="btn"><?= t('index.register') ?></a>
    </div>
  </div>
  <audio id="bg-music" src="../Audio/Test Drive.mp3" loop autoplay></audio>
  <script src="../js/script.js"></script>
</body>

</html>