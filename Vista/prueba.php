<?php
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
  <!-- Botón volver (historial) -->
  <button class="back-btn" onclick="window.location.href='menu.php'" title="Volver al menú">&#8592;</button>

  <!-- Botón "i" para reabrir la superposición -->
  <button id="parque-info-btn" class="info-top" aria-label="Información">i</button>

  <!-- Overlay informativo (oculto por defecto con .hidden) -->
  <div id="tool-overlay" class="tool-overlay hidden" role="dialog" aria-modal="true" aria-labelledby="tool-title">
    <div class="tool-modal" role="document">
      <button id="tool-close" class="tool-close" aria-label="Cerrar">×</button>
      <h2 id="tool-title">Bienvenido al Modo Herramienta</h2>
      <p>En este modo podrás calcular los puntos, visualizar cuál puede ser tu mejor movimiento para maximizar tu puntaje y ganar tu partida de Draftosaurus!</p>
    </div>
  </div>

  <div class="container">
    <div class="header">
      <h1>🦖 Parque de Dinosaurios 🦕</h1>
      <p>Arrastra los dinosaurios a las zonas del parque</p>
    </div>

    <div class="inventory">
      <h2>📊 Inventario Global</h2>
      <div class="inventory-grid" id="inventory-grid"></div>
    </div>

    <div class="main-content">
      <div class="dino-panel">
        <h3>🦴 Disponibles</h3>
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
      <h3>🏆 Puntaje por zona</h3>
      <ul id="zone-scores" style="list-style:none; padding:0; margin:0;">
        <!-- Puntajes se llenan por JS -->
      </ul>
    </div>
  </div>
  <audio id="bg-music" src="../Audio/Test Drive.mp3" loop autoplay></audio>
  <script src="../js/script.js"></script>
</body>

</html>