<?php require __DIR__ . '/../lang/boot.php'; ?>

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
      <svg viewBox="0 0 24 24">
        <path d="M12 15.5A3.5 3.5 0 1 0 12 8.5a3.5 3.5 0 0 0 0 7zm7.43-2.88l1.77-1.02a.5.5 0 0 0 .18-.68l-1.68-2.91a.5.5 0 0 0-.61-.23l-2.08.83a7.03 7.03 0 0 0-1.61-.93l-.32-2.21a.5.5 0 0 0-.5-.42h-3.36a.5.5 0 0 0-.5.42l-.32 2.21c-.57.22-1.11.52-1.61.93l-2.08-.83a.5.5 0 0 0-.61.23l-1.68 2.91a.5.5 0 0 0 .18.68l1.77 1.02c-.04.32-.07.65-.07.98s.03.66.07.98l-1.77 1.02a.5.5 0 0 0-.18.68l1.68 2.91a.5.5 0 0 0 .61.23l2.08-.83c.5.41 1.04.71 1.61.93l.32 2.21a.5.5 0 0 0 .5.42h3.36a.5.5 0 0 0 .5-.42l.32-2.21c.57-.22 1.11-.52 1.61-.93l2.08.83a.5.5 0 0 0 .61-.23l1.68-2.91a.5.5 0 0 0-.18-.68l-1.77-1.02c.04-.32.07-.65.07-.98s-.03-.66-.07-.98z" />
      </svg>
    </button>

    <!-- Logo -->
    <img src="../img/logodraftosaurus.png" alt="Draftosaurus Logo" class="logo-menu">

    <!-- Botones menú -->
    <div class="menu-botones">
      <a href="searchgame.php" class="btn menu-btn"><?= t('menu.find') ?></a>
      <a href="#" class="btn menu-btn"><?= t('menu.create') ?></a>
      <a href="#" class="btn menu-btn"><?= t('menu.tools') ?></a>
      <a href="#" class="btn menu-btn"><?= t('menu.howto') ?></a>
      <a href="../Controlador/logout.php" class="btn menu-btn"><?= t('menu.logout') ?></a>
    </div>


    <?php
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    ?>
    <!-- Overlay de perfil -->
    <div id="perfil-overlay" class="perfil-overlay is-hidden" role="dialog" aria-modal="true" aria-labelledby="perfil-titulo">
      <div class="perfil-modal">
        <button type="button" class="close-perfil" aria-label="Cerrar" onclick="cerrarPerfilOverlay()">×</button>

        <!-- Encabezado -->
        <header class="perfil-header">
          <h2 id="perfil-titulo" class="perfil-titulo">Perfil</h2>
        </header>

        <!-- Layout principal -->
        <section class="perfil-grid" role="group" aria-label="Resumen de perfil">
          <!-- Columna izquierda: avatar + cambiar foto -->
          <div class="perfil-avatar">
            <figure class="perfil-foto-wrapper">
              <img id="perfil-foto" src="../img/perfil.png" alt="Foto de perfil" class="perfil-foto">
            </figure>

            <form id="perfil-foto-form" class="perfil-foto-form" enctype="multipart/form-data" method="post" action="../Controlador/perfil_foto.php">
              <input type="file" id="foto-input" name="foto" accept="image/*">
              <button type="submit" class="perfil-foto-btn">Cambiar foto</button>
            </form>
          </div>

          <!-- Centro: usuario -->
          <div class="perfil-usuario">
            <span class="perfil-label">Usuario:</span>
            <span id="perfil-username" class="perfil-username">
              <?= htmlspecialchars($_SESSION['nombre'] ?? 'Invitado') ?>
            </span>
          </div>

          <!-- Derecha: nivel -->
          <div class="perfil-nivel">
            <span class="perfil-label">Nivel</span>
            <span id="perfil-nivel" class="perfil-nivel-num">x</span>
          </div>

          <!-- Fila de estadísticas -->
          <dl class="perfil-stats">
            <div class="stat">
              <dt>Partidas jugadas:</dt>
              <dd id="stat-jugadas">x</dd>
            </div>
            <div class="stat">
              <dt>Partidas ganadas:</dt>
              <dd id="stat-ganadas">x</dd>
            </div>
            <div class="stat">
              <dt>Puntaje más alto:</dt>
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
  </div>
  </div>

  <audio id="bg-music" src="../Audio/musica1.mp3" loop autoplay></audio>

  <script src="../js/script.js"></script>
</body>

</html>