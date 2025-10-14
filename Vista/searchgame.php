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
    <button class="back-btn" onclick="window.location.href='menu.php'" title="Volver al menú">&#8592;</button>
    <!-- Botones menú -->
    <div class="menu-botonespro">
      <a class="btn menu-btnpro"><?= t('menu.find') ?></a>
      <a href="servers.php" class="btn menu-btn"><?= t('menu.servers') ?></a>
      <a href="#" class="btn menu-btn"><?= t('menu.searchcode') ?></a>
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
    </div>
  </div>
  </div>

  <audio id="bg-music" src="../Audio/musica1.mp3" loop autoplay></audio>

  <script src="../js/script.js"></script>
</body>

</html>