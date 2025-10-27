<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Servidores - Draftosaurus</title>
  <link rel="stylesheet" href="../Css/styles.css">
  <link rel="stylesheet" href="../Css/servers.css">
  <link rel="stylesheet" href="../Css/media.css">
  <link href="https://fonts.googleapis.com/css2?family=Agbalumo&display=swap" rel="stylesheet">
</head>

<body>
  <div class="borde top"></div>
  <div class="borde bottom"></div>
  <div class="borde left"></div>
  <div class="borde right"></div>
  
  <!-- Dinosaurio Abajo a La Derecha -->
  <div class="overlay-img">
    <img src="../img/dino.png" alt="Dinosaurio decorativo">
  </div>

  <div class="container">
    <button class="back-btn" onclick="handleBackButton()" title="Volver al menú">
      <img src="../img/botonatras.png" alt="Volver al menú" class="back-btn-img">
    </button>

    <main class="servers-center">
      <section class="servers-container" role="region" aria-label="Lista de servidores">
        <!-- Título en tabla de madera colgante -->
        <div class="servers-title-overlay" aria-hidden="true">Servidores</div>
        
        <div class="server-list">
          <!-- Servidor 1 -->
          <div class="server-row">
            <span class="server-icon">🦕</span>
            <span class="server-sala">Partida 01</span>
            <span class="server-host">DragonKing</span>
            <span class="server-players">0/4 jug</span>
            <span class="server-map">Mapa Verano</span>
            <div class="server-action">
              <button class="join-btn small" onclick="location.href='game.php'" title="Unirse"></button>
            </div>
          </div>

          <!-- Servidor 2 - CON CANDADO -->
          <div class="server-row locked">
            <span class="server-icon">🔐</span>
            <span class="server-sala">Partida 02</span>
            <span class="server-host">GenSoftware</span>
            <span class="server-players">0/4 jug</span>
            <span class="server-map">Mapa Invierno</span>
            <div class="server-action">
              <button class="join-btn small" disabled title="Sala protegida"></button>
              <span class="lock-overlay" aria-hidden="true">🔒</span>
            </div>
          </div>

          <!-- Servidor 3 -->
          <div class="server-row">
            <span class="server-icon">🦖</span>
            <span class="server-sala">Partida 03</span>
            <span class="server-host">Raptor95</span>
            <span class="server-players">0/4 jug</span>
            <span class="server-map">Mapa Verano</span>
            <div class="server-action">
              <button class="join-btn small" onclick="location.href='game.php'" title="Unirse"></button>
            </div>
          </div>

          <!-- Servidor 4 -->
          <div class="server-row">
            <span class="server-icon">🌍</span>
            <span class="server-sala">Partida 04</span>
            <span class="server-host">JurassicWorld</span>
            <span class="server-players">0/4 jug</span>
            <span class="server-map">Mapa Invierno</span>
            <div class="server-action">
              <button class="join-btn small" onclick="location.href='game.php'" title="Unirse"></button>
            </div>
          </div>

          <!-- Servidor 5 - CON CANDADO -->
          <div class="server-row locked">
            <span class="server-icon">🦕</span>
            <span class="server-sala">Partida 05</span>
            <span class="server-host">Chinchulito24</span>
            <span class="server-players">0/4 jug</span>
            <span class="server-map">Mapa Verano</span>
            <div class="server-action">
              <button class="join-btn small" disabled title="Sala protegida"></button>
              <span class="lock-overlay" aria-hidden="true">🔒</span>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
  
  <script src="../js/script.js"></script>
  <audio id="volver-audio" src="../Audio/sonidoatras.mp3"></audio>
</body>

</html>