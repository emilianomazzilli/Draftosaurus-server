<?php require __DIR__ . '/../lang/boot.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= t('searchgame.title') ?></title>
  <link rel="stylesheet" href="../Css/styles.css">
  <link rel="stylesheet" href="../Css/menu.css">
  <link rel="stylesheet" href="../Css/media.css">
  <link href="https://fonts.googleapis.com/css2?family=Agbalumo&display=swap" rel="stylesheet">
</head>

<body>
  <!-- Hojas cayendo -->
  <div class="leaves" id="leaves"></div>
  <div class="borde top"></div>
  <div class="borde bottom"></div>
  <div class="borde left"></div>
  <div class="borde right"></div>
  <!-- Dinosaruio Abajo a La Derecha todo pro -->
  <div class="overlay-img">
    <img src="../img/dino.png">
  </div>

  <div class="container menu">
    <!-- Botón volver -->
    <button class="back-btn" onclick="handleBackButton()" title="Volver al menú">
      <img src="../img/botonatras.png" alt="Volver al menú" class="back-btn-img">
    </button>

    <!-- Botones menú -->
    <div class="menu-botonespro">
      <a class="butn menu-btnpro"><?= t('searchgame.find') ?></a>
      <a href="creategame.php" class="btn menu-btn"><?= t('searchgame.create') ?></a>
      <a href="servers.php" class="btn menu-btn"><?= t('searchgame.servers') ?></a>
      <a href="#" class="btn menu-btn" id="buscar-codigo-btn"><?= t('searchgame.searchcode') ?></a>
    </div>

    <!-- Overlay de buscar código -->
    <div id="codigo-overlay" class="codigo-overlay" style="display: none;">
      <div class="codigo-modal">
        <button type="button" class="close-codigo" onclick="cerrarCodigoOverlay()">×</button>
        
        <header class="codigo-header">
          <h2 class="codigo-titulo"><?= t('searchgame.code') ?></h2>
        </header>

        <form id="codigo-form" class="codigo-form">
          <div class="codigo-input-wrapper">
            <input 
              type="text" 
              id="codigo-input" 
              name="codigo" 
              placeholder="<?= t('searchgame.code2') ?>"
              maxlength="20"
              autocomplete="off"
              required
            >
          </div>
          
          <button type="submit" class="codigo-submit-btn">
            <?= t('searchgame.searchgame') ?>
          </button>
        </form>
      </div>
    </div>
  </div>

  <audio id="bg-music" src="../Audio/Test Drive.mp3" loop autoplay></audio>
  <audio id="volver-audio" src="../Audio/sonidoatras.mp3"></audio>
  <script src="../js/script.js"></script>
</body>

</html>