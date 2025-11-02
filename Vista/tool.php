<?php
require __DIR__ . '/../lang/boot.php';
// login.php - Vista de inicio de sesión
if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
  ]);
  session_start();
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
  <link rel="stylesheet" href="../Css/tool.css">
  <link href="https://fonts.googleapis.com/css2?family=Agbalumo&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../Css/media.css">
</head>

<body>
  <div class="borde top"></div>
  <div class="borde bottom"></div>
  <div class="borde left"></div>
  <div class="borde right"></div>
  
  <!-- Hojas cayendo -->
  <div class="leaves" id="leaves"></div>

  <!-- Dinosaruio Abajo a La Derecha todo pro -->
  <div class="overlay-img">
    <img src="../img/dino.png">
  </div>
  <!-- Botón volver (historial) -->
  <button class="back-btn" onclick="window.location.href='menu.php'" title="Volver al menú"><img src="../img/botonatras.png" alt="Volver al menú" class="back-btn-img"></button>

  <!-- Botón "i" para reabrir la superposición -->
  <button id="parque-info-btn" class="info-top" aria-label="Información">i</button>

  <!-- Overlay informativo (oculto por defecto con .hidden) -->
  <div id="tool-overlay" class="tool-overlay hidden" role="dialog" aria-modal="true" aria-labelledby="tool-title">
    <div class="tool-modal" role="document">
      <!-- Flecha izquierda -->
      <button id="tool-prev" class="tool-nav left" aria-label="Anterior" title="Anterior" disabled>‹</button>

      <!-- Carrusel -->
      <div class="tool-carousel" aria-live="polite">
        <div class="tool-carousel-track">
          <!-- Slide 0 (original) -->
          <section class="tool-slide" data-index="0">
            <h2 id="tool-title"><?= t('tool.welcome') ?></h2>
            <p><?= t('tool.welcome_description1') ?></p>
            <p><?= t('tool.welcome_description2') ?></p>
          </section>

          <!-- Slide 1 (ejemplo: instrucciones) -->
          <section class="tool-slide" data-index="1">
            <h2><?= t('tool.howto') ?></h2>
            <ul>
              <li><?= t('tool.instruction1') ?></li>
              <li><?= t('tool.instruction2') ?></li>
              <li><?= t('tool.instruction3') ?></li>
            </ul>
          </section>

          <!-- Slide 2 (ejemplo: atajos) -->
          <section class="tool-slide" data-index="2">
            <h2><?= t('tool.shortcuts') ?></h2>
            <p><?= t('tool.shortcuts_description') ?></p>
          </section>
        </div>

        <!-- indicadores (puntos) -->
        <div class="tool-indicators" aria-hidden="true"></div>
      </div>

      <!-- Flecha derecha -->
      <button id="tool-next" class="tool-nav right" aria-label="Siguiente" title="Siguiente">›</button>

      <!-- Botón cerrar -->
      <button id="tool-close" class="tool-close" aria-label="Cerrar">×</button>
    </div>
  </div>

  <div class="container">
    <div class="inventory">
      <h2><?= t('tool.global_inventory') ?></h2>
      <div class="inventory-grid" id="inventory-grid"></div>
    </div>

    <div class="main-content">
      <div class="dino-panel">
        <h3>🦴<?= t('tool.available') ?></h3>
        <div class="dino-list" id="dino-list"></div>
      </div>

      <div class="map-container">
        <div class="map-wrapper">
          <img src="../img/tablero.png" alt="Mapa del Parque" class="map-image">
          <div id="zones-container"></div>
        </div>
      </div>
    <!-- Panel de puntajes por zona -->
    <div class="dino-panel" id="score-panel">
      <h3>🏆<?= t('tool.zone_score') ?></h3>
      <ul id="zone-scores" style="list-style:none; padding:0; margin:0;">
        <!-- Puntajes se llenan por JS -->
      </ul>
    </div>
  </div>
  <audio id="bg-music" src="../Audio/Test Drive.mp3" loop autoplay></audio>
  <audio id="volver-audio" src="../Audio/sonidoatras.mp3"></audio>
  <script src="../js/script.js"></script>
</body>

</html>