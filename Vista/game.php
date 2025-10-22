<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Partida - Draftosaurus</title>

  <!-- Estilos -->
  <link rel="stylesheet" href="../Css/styles.css" />
  <link rel="stylesheet" href="../Css/game.css" />
  <link rel="stylesheet" href="../Css/media.css" />
  <link href="https://fonts.googleapis.com/css2?family=Agbalumo&display=swap" rel="stylesheet" />
</head>

<body>
  <!-- Bordes decorativos -->
  <div class="borde top"></div>
  <div class="borde bottom"></div>
  <div class="borde left"></div>
  <div class="borde right"></div>

  <!-- Contenedor principal del juego (grid) -->
  <div class="game-container">
    <!-- Título / encabezado -->

    <!-- Información de la partida -->
    <div class="game-info">
      <p>Jugador: UsuarioEjemplo</p>
      <p>Turno: 2</p>
    </div>
    <div class="game-info">
      <p>Jugador: UsuarioEjemplo</p>
      <p>Turno: 2</p>
    </div>

    <!-- Tablero central -->
    <div class="board-container">
      <div class="board-title">Tablero</div>

      <div class="board-wrapper">
        <img src="../img/tablero.png" alt="Mapa del Parque" class="map-image" />
        <!-- Zonas interactivas superpuestas al tablero -->
        <div id="zones-container"></div>
      </div>
    </div>

    <!-- Controles inferiores -->
    <div class="controls">
      <button class="btn action" type="button">Realizar Acción</button>
      <button class="btn end-turn" type="button">Terminar Turno</button>
    </div>
  </div>

  <!-- Lógica del juego -->
  <script src="../js/script.js"></script>
</body>
</html>
