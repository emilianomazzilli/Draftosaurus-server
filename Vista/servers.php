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

  <div class="container">
    <button class="back-btn" onclick="window.location.href='searchgame.php'" title="Volver al menú">&#8592;</button>

    <main class="servers-center">
      <section class="servers-container" role="region" aria-label="Lista de servidores">
        <div class="servers-title-overlay" aria-hidden="true">Servidores</div>
        
        <div class="server-list">
          <div class="server-row">
            <div class="server-left">
              <span class="server-icon">🦕</span>
              <span class="server-name">Sala 1 <span class="server-meta">N° Jug: 2/6 &nbsp; | &nbsp; Mapa</span></span>
            </div>
            <div class="server-action">
              <button class="join-btn small" onclick="location.href='game.php'" title="Unirse"></button>
            </div>
          </div>

          <div class="server-row locked">
            <div class="server-left">
              <span class="lock-static" aria-hidden="true">🔒</span>
              <span class="server-icon">🦖</span>
              <span class="server-name">GenSoftware <span class="server-meta">N° Jug: 4/6 &nbsp; | &nbsp; Mapa</span></span>
            </div>
            <div class="server-action">
              <button class="join-btn small" disabled title="Sala protegida"></button>
              <span class="lock-hover" aria-hidden="true">🔒</span>
            </div>
          </div>

          <div class="server-row">
            <div class="server-left">
              <span class="server-icon">🦕</span>
              <span class="server-name">Sala 2 <span class="server-meta">N° Jug: 3/6 &nbsp; | &nbsp; Mapa</span></span>
            </div>
            <div class="server-action">
              <button class="join-btn small" onclick="location.href='game.php'" title="Unirse"></button>
            </div>
          </div>

          <div class="server-row locked">
            <div class="server-left">
              <span class="lock-static" aria-hidden="true">🔒</span>
              <span class="server-icon">🌎</span>
              <span class="server-name">Chinchulito24 <span class="server-meta">N° Jug: 4/6 &nbsp; | &nbsp; Mapa</span></span>
            </div>
            <div class="server-action">
              <button class="join-btn small" disabled title="Sala protegida"></button>
              <span class="lock-hover" aria-hidden="true">🔒</span>
            </div>
          </div>

          <div class="server-row">
            <div class="server-left">
              <span class="server-icon">🦕</span>
              <span class="server-name">Sala 3 <span class="server-meta">N° Jug: 3/6 &nbsp; | &nbsp; Mapa</span></span>
            </div>
            <div class="server-action">
              <button class="join-btn small" onclick="location.href='../probandoeljuego.php'" title="Unirse"></button>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
  <script src="../js/script.js"></script>
</body>

</html>