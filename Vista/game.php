<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Partida - Draftosaurus</title>
  <link rel="stylesheet" href="../Css/styles.css">
  <link rel="stylesheet" href="../Css/game.css">
  <link rel="stylesheet" href="../Css/media.css">
  <link href="https://fonts.googleapis.com/css2?family=Agbalumo&display=swap" rel="stylesheet">
</head>

<body>
  <div class="container game-container">
    <!-- Tableros de otros jugadores -->
    <div class="other-boards">
      <div class="player-board">
        <div class="player-table-bar">
          <img src="../img/tabla.png" alt="Tabla jugador" class="player-table-img shadow-table">
          <span class="player-name on-table">Jugador 2</span>
        </div>
        <div class="board-bg">
          <img src="../img/tablero.png" alt="Tablero jugador 2" class="other-board">
        </div>
        <div class="player-dino-photo">
          <img src="../img/dino1.png" alt="Dinosaurio jugador 2" class="player-dino-img">
        </div>
      </div>
      <div class="player-board">
        <div class="player-table-bar">
          <img src="../img/tabla.png" alt="Tabla jugador" class="player-table-img shadow-table">
          <span class="player-name on-table">Jugador 3</span>
        </div>
        <div class="board-bg">
          <img src="../img/tablero.png" alt="Tablero jugador 3" class="other-board">
        </div>
        <div class="player-dino-photo">
          <img src="../img/dino2.png" alt="Dinosaurio jugador 3" class="player-dino-img">
        </div>
      </div>
      <div class="player-board">
        <div class="player-table-bar">
          <img src="../img/tabla.png" alt="Tabla jugador" class="player-table-img shadow-table">
          <span class="player-name on-table">Jugador 4</span>
        </div>
        <div class="board-bg">
          <img src="../img/tablero.png" alt="Tablero jugador 4" class="other-board">
        </div>
        <div class="player-dino-photo">
          <img src="../img/dino3.png" alt="Dinosaurio jugador 4" class="player-dino-img">
        </div>
      </div>
    </div>
    <!-- Tablero principal -->
    <div class="main-board-section">
      <div class="main-board-label">Nombre jugador</div>
      <div class="main-board-bg">
        <img src="../img/tablero.png" alt="Tablero principal" class="main-board" id="main-board-img">
        <div id="tablero-casillas">
          <div class="casilla" data-casilla="1" style="left:6.6667%; top:15%;"></div>
          <div class="casilla" data-casilla="2" style="left:20%;     top:20%;"></div>
          <div class="casilla" data-casilla="3" style="left:33.3333%; top:15%;"></div>
          <div class="casilla" data-casilla="4" style="left:46.6667%; top:25%;"></div>
          <div class="casilla" data-casilla="5" style="left:60%;      top:20%;"></div>
          <div class="casilla" data-casilla="6" style="left:73.3333%; top:15%;"></div>
          <div class="casilla" data-casilla="7" style="left:13.3333%; top:50%;"></div>
          <div class="casilla" data-casilla="8" style="left:26.6667%; top:55%;"></div>
          <div class="casilla" data-casilla="9" style="left:40%;      top:50%;"></div>
          <div class="casilla" data-casilla="10" style="left:53.3333%; top:55%;"></div>
          <div class="casilla" data-casilla="11" style="left:66.6667%; top:50%;"></div>
          <div class="casilla" data-casilla="12" style="left:80%;      top:55%;"></div>
        </div>

      </div>
    </div>
    <!-- Tabla de dinosaurios y dado -->
    <div class="dino-table-section">
      <button class="dice-btn" title="Lanzar dado" onclick="mostrarOverlay()">🎲</button>
      <div class="dino-table">
        <div class="dino" title="Dino rojo" draggable="true" data-dino="rojo">🦖</div>
        <div class="dino" title="Dino amarillo" draggable="true" data-dino="amarillo">🦕</div>
        <div class="dino" title="Dino naranja" draggable="true" data-dino="naranja">🦑</div>
        <div class="dino" title="Dino violeta" draggable="true" data-dino="violeta">🦐</div>
        <div class="dino" title="Dino azul" draggable="true" data-dino="azul">🐉</div>
        <div class="dino" title="Dino verde" draggable="true" data-dino="verde">🦎</div>
      </div>
      <div class="timer-section">
        <span class="timer-label">60 seg.</span>
      </div>
    </div>

    <!-- Overlay de puntaje final -->
    <div id="puntaje-overlay" class="puntaje-overlay" style="display:none;">
      <div class="puntaje-modal">
        <div class="puntaje-header">Puntaje Final</div>
        <ul class="puntaje-list">
          <li><span class="puntaje-icon">🦖</span> <b>Tú</b> <span class="puntaje-puntos">44 Pts.</span> <span class="puntaje-pos">1°</span>
          </li>
          <li><span class="puntaje-icon">🦕</span> Player 2 <span class="puntaje-puntos">28 Pts.</span> <span class="puntaje-pos">2°</span>
          </li>
          <li><span class="puntaje-icon">🦑</span> Player 3 <span class="puntaje-puntos">16 Pts.</span> <span class="puntaje-pos">3°</span>
          </li>
          <li><span class="puntaje-icon">🦐</span> Player 4 <span class="puntaje-puntos">4 Pts.</span> <span class="puntaje-pos">4°</span>
          </li>
        </ul>
        <div class="puntaje-mensaje">
          <b>Has quedado en la posición:</b> <span style="color:#d97a2b;font-size:1.3rem;">1°</span><br>
          <span style="color:#1976d2;">¡Eres el Dinosaurio Supremo!</span>
        </div>
        <button class="btn aceptar" onclick="window.location.href='../Vista/menu.php'">Aceptar</button>
      </div>
    </div>
  </div>
  <script src="../js/script.js"></script>

</body>

</html>