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
    <svg viewBox="0 0 24 24">
      <path d="M12 15.5A3.5 3.5 0 1 0 12 8.5a3.5 3.5 0 0 0 0 7zm7.43-2.88l1.77-1.02a.5.5 0 0 0 .18-.68l-1.68-2.91a.5.5 0 0 0-.61-.23l-2.08.83a7.03 7.03 0 0 0-1.61-.93l-.32-2.21a.5.5 0 0 0-.5-.42h-3.36a.5.5 0 0 0-.5.42l-.32 2.21c-.57.22-1.11.52-1.61.93l-2.08-.83a.5.5 0 0 0-.61.23l-1.68 2.91a.5.5 0 0 0 .18.68l1.77 1.02c-.04.32-.07.65-.07.98s.03.66.07.98l-1.77 1.02a.5.5 0 0 0-.18.68l1.68 2.91a.5.5 0 0 0 .61.23l2.08-.83c.5.41 1.04.71 1.61.93l.32 2.21a.5.5 0 0 0 .5.42h3.36a.5.5 0 0 0 .5-.42l.32-2.21c.57-.22 1.11-.52 1.61-.93l2.08.83a.5.5 0 0 0 .61-.23l1.68-2.91a.5.5 0 0 0-.18-.68l-1.77-1.02c.04-.32.07-.65.07-.98s-.03-.66-.07-.98z" />
    </svg>
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
          <div class="opcion-label">
            <span><b>Modo oscuro</b></span>
          </div>
          <label class="switch-madera">
            <input type="checkbox" id="toggle-dark" disabled>
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
    <img src="../img/dinopng.png">
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
    <img src="../img/logodraftosaurus.png" alt="Draftosaurus Logo" class="logo-draftosaurus" loading="lazy">
    <div class="bottom-buttons">
      <a href="../Vista/login.php" class="btn"><?= t('index.login') ?></a>
      <a href="../Controlador/agregar.php" class="btn"><?= t('index.register') ?></a>
    </div>
  </div>
  <script src="../js/script.js"></script>
</body>

</html>