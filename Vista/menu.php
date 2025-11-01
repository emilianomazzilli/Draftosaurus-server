<?php
require __DIR__ . '/../lang/boot.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= t('menu.title') ?></title>
  <link rel="stylesheet" href="../Css/styles.css">
  <link rel="stylesheet" href="../Css/menu.css">
  <link rel="stylesheet" href="../Css/media.css">
  <link href="https://fonts.googleapis.com/css2?family=Agbalumo&display=swap" rel="stylesheet">
</head>

<body>
  <div class="borde top"></div>
  <div class="borde bottom"></div>
  <div class="borde left"></div>
  <div class="borde right"></div>
  <div class="container menu">
    <!-- Botón de perfil -->
     
     <button id="perfil-btn" class="perfil-btn" title="<?= t('menu.profile') ?>">
       <div class="perfil-icon-wrapper">
         <img src="../img/perfil.png" alt="<?= t('menu.profile') ?>" class="perfil-icon-img">
        </div>
      </button>
      <!-- Botón de opciones -->
      <button id="opciones-btn" class="opciones-btn" title="<?= t('menu.options') ?>">
        <img src="../img/engranaje.png" alt="<?= t('menu.options') ?>" class="opciones-icon">
      </button>
    

    <!-- Logo -->
    <img src="../img/logodraftosaurus.png" alt="Draftosaurus Logo" class="logo-menu">

    <!-- Botones menú -->
    <div class="menu-botones">
      <a href="searchgame.php" class="btn menu-btn"><?= t('menu.find') ?></a>
      <a href="creategame.php" class="btn menu-btn"><?= t('menu.create') ?></a>
      <a href="tool.php" class="btn menu-btn"><?= t('menu.tools') ?></a>
      <a href="../clasificacionmejorada.html" class="btn menu-btn"><?= t('menu.howto') ?></a>
      <a href="../Controlador/logout.php" class="btn menu-btn"><?= t('menu.logout') ?></a>
    </div>


    <!-- Overlay de perfil -->
    <div id="perfil-overlay" class="perfil-overlay" style="display: none;">
      <div class="perfil-modal">
        <button type="button" class="close-perfil" aria-label="Cerrar" onclick="cerrarPerfilOverlay()">×</button>
        <header class="perfil-header">
          <h2 id="perfil-titulo" class="perfil-titulo"><?= t('menu.perfil') ?></h2>
        </header>

        <!-- Layout principal -->
        <section class="perfil-grid" role="group" aria-label="Resumen de perfil">
          <!-- Columna izquierda: avatar + cambiar foto -->
          <div class="perfil-avatar">
            <figure class="perfil-foto-wrapper">
              <img id="perfil-foto" src="../img/perfil.png" alt="Foto de perfil" class="perfil-foto">
            </figure>
            <form id="perfil-foto-form" class="perfil-foto-form">
              <input type="file" id="foto-input" name="foto" accept="image/*" style="display: none;">
              <button type="button" class="perfil-foto-btn" onclick="document.getElementById('foto-input').click()">
                <?= t('menu.changephoto') ?>
              </button>
              <div id="perfil-foto-error" class="error-message" aria-live="polite"></div>
            </form>
          </div>

          <!-- Centro: usuario -->
          <div class="perfil-usuario">
            <span class="perfil-label"><?= t('menu.user') ?></span>
            <span id="perfil-username" class="perfil-username">
              <?= htmlspecialchars($_SESSION['nombre'] ?? 'Invitado') ?>
            </span>
            <!-- Nuevo: email -->
            <div class="perfil-email-row" style="margin-top:6px;">
              <span class="perfil-label"><?= t('menu.email') ?></span>
              <span id="perfil-email" class="perfil-email">
                <?= htmlspecialchars($_SESSION['email'] ?? '') ?>
              </span>
            </div>
          </div>

          <!-- Fila de estadísticas -->
          <dl class="perfil-stats">
            <div class="stat">
              <dt><?= t('menu.gamesplayed') ?></dt>
              <dd id="stat-jugadas">x</dd>
            </div>
            <div class="stat">
              <dt><?= t('menu.gameswinned') ?></dt>
              <dd id="stat-ganadas">x</dd>
            </div>
            <div class="stat">
              <dt><?= t('menu.highscore') ?></dt>
              <dd id="stat-max">x</dd>
            </div>
          </dl>
        </section>
      </div>
    </div>


    <!-- Overlay de opciones -->
    <div id="opciones-overlay" class="opciones-overlay" style="display:none;">
      <div class="opciones-modal-wrapper">
      </div>
      <div class="opciones-modal">
        <span class="opciones-nombre-titulo"><?= t('menu.options') ?></span>
        <span class="close-opciones" onclick="cerrarOpcionesOverlay()">×</span>
        <div class="opciones-contenido">
          <div class="opcion-control">
            <label for="sonido-range"><b><?= t('menu.sound') ?></b></label>
            <input type="range" id="sonido-range" min="0" max="100" value="50" oninput="actualizarSonido(this.value)">
            <span id="sonido-valor">50</span>%
          </div>
          <div class="opcion-control">
            <label for="musica-range"><b><?= t('menu.music') ?></b></label>
            <input type="range" id="musica-range" min="0" max="100" value="50" oninput="actualizarMusica(this.value)">
            <span id="musica-valor">50</span>%
          </div>
          <div class="opcion-item">
            <span><?= t('menu.darkmode') ?></span>
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
  </div>
  </div>

  <audio id="bg-music" src="../Audio/Test Drive.mp3" loop autoplay></audio>

  <script src="../js/script.js"></script>
</body>

</html>