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
    <!-- Botón volver -->
    <button class="back-btn" onclick="window.location.href='menu.php'" title="Volver al menú"><img src="../img/flecha.png"></button>

    <!-- Botones menú -->
    <div class="menu-botonespro">
      <a class="butn menu-btnpro"><?= t('menu.find') ?></a>
      <a href="servers.php" class="btn menu-btn"><?= t('menu.servers') ?></a>
      <a href="#" class="btn menu-btn" id="buscar-codigo-btn"><?= t('menu.searchcode') ?></a>
    </div>

    <!-- Overlay de perfil (mínimo) -->
    <div id="perfil-overlay" class="perfil-overlay is-hidden" role="dialog" aria-modal="true" aria-labelledby="perfil-titulo">
      <div class="perfil-modal">
        <button type="button" class="close-perfil" aria-label="Cerrar" onclick="cerrarPerfilOverlay()">×</button>
        <header class="perfil-header">
          <h2 id="perfil-titulo" class="perfil-titulo">Perfil</h2>
        </header>
        <section class="perfil-grid" role="group" aria-label="Resumen de perfil">
          <div class="perfil-avatar">
            <img id="perfil-foto" src="../img/perfil.png" alt="Foto de perfil" class="perfil-foto">
          </div>
          <div class="perfil-usuario">
            <span class="perfil-label">Usuario:</span>
            <span id="perfil-username" class="perfil-username"><?= htmlspecialchars($_SESSION['nombre'] ?? 'Invitado') ?></span>
          </div>
        </section>
      </div>
    </div>

    <!-- Overlay de buscar código -->
    <div id="codigo-overlay" class="codigo-overlay">
      <div class="codigo-modal">
        <button type="button" class="close-codigo" aria-label="Cerrar" onclick="cerrarCodigoOverlay()">×</button>
        
        <header class="codigo-header">
          <h2 class="codigo-titulo">Ingresar Código</h2>
        </header>

        <form id="codigo-form" class="codigo-form">
          <div class="codigo-input-wrapper">
            <input 
              type="password" 
              id="codigo-input" 
              name="codigo" 
              placeholder="Ingresa el código"
              maxlength="20"
              autocomplete="off"
              required
            >
            <button type="button" class="toggle-password" id="toggle-password" aria-label="Mostrar/Ocultar">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </button>
          </div>
          
          <button type="submit" class="codigo-submit-btn">
            🔍 Buscar Partida
          </button>
        </form>
      </div>
    </div>
  </div>

  <audio id="bg-music" src="../Audio/Test Drive.mp3" loop autoplay></audio>
  <script src="../js/script.js"></script>
</body>

</html>