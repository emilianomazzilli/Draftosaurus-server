<?php
require __DIR__ . '/../lang/boot.php';
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Simular jugadores (en producción vendría de la BD)
$players = [
  [
    'id' => $_SESSION['user_id'] ?? 1,
    'name' => $_SESSION['username'] ?? 'Usuario',
    'avatar' => '../img/perfil.png',
    'isHost' => true
  ],
  [
    'id' => 2,
    'name' => 'Jugador 2',
    'avatar' => '../img/dino1.png',
    'isHost' => false
  ],
  [
    'id' => 3,
    'name' => 'Jugador 3',
    'avatar' => '../img/dino2.png',
    'isHost' => false
  ],
  [
    'id' => 4,
    'name' => 'Jugador 4',
    'avatar' => '../img/dino3.png',
    'isHost' => false
  ]
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lobby - Draftosaurus</title>
  <link rel="stylesheet" href="../Css/styles.css">
  <link rel="stylesheet" href="../Css/lobby.css">
  <link rel="stylesheet" href="../Css/media.css">
  <link href="https://fonts.googleapis.com/css2?family=Agbalumo&display=swap" rel="stylesheet">
</head>

<body>
  <div class="borde top"></div>
  <div class="borde bottom"></div>
  <div class="borde left"></div>
  <div class="borde right"></div>
  <!-- Dinosaruio Abajo a La Derecha todo pro -->
  <div class="overlay-img">
    <img src="../img/dino.png">
  </div>

  <div class="container lobby-page">
    <button class="back-btn" onclick="handleBackButton()" title="Volver al menú">
      <img src="../img/botonatras.png" alt="Volver al menú" class="back-btn-img">
    </button>

    <div class="title-banner">Sala de espera</div>

    <div class="panel pergamino">
      <div class="players-list">
        <?php foreach ($players as $index => $player): ?>
          <div class="player-row" data-player-id="<?= $player['id'] ?>">
            <div class="player-info">
              <img src="<?= $player['avatar'] ?>" alt="Avatar" class="player-avatar">
              <span class="player-name"><?= htmlspecialchars($player['name']) ?></span>
            </div>
            <div class="player-status">
              <span class="ping-value">--</span>
              <span class="ping-indicator"></span>
              <?php if (!$player['isHost']): ?>
                <button class="kick-btn" title="Expulsar jugador">✕</button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="ready-section">
      <button id="ready-btn" class="btn ready">¿Listo?</button>
      <div id="countdown" class="countdown"></div>
    </div>
  </div>

  <script>
    // Simulación de ping
    function updatePings() {
      document.querySelectorAll('.player-row').forEach((row, index) => {
        const pingValue = row.querySelector('.ping-value');
        const indicator = row.querySelector('.ping-indicator');

        // El último jugador tiene ping alto
        let ping;
        if (index === 3) {
          ping = Math.floor(Math.random() * 4000) + 1000; // 1000-5000ms
        } else {
          ping = Math.floor(Math.random() * 100) + 1; // 1-100ms para los demás
        }

        // Actualizar valor y color
        pingValue.textContent = ping + 'ms';
        if (ping <= 100) {
          indicator.style.backgroundColor = '#4CAF50';
        } else if (ping <= 750) {
          indicator.style.backgroundColor = '#FFC107';
        } else {
          indicator.style.backgroundColor = '#f44336';
        }
      });
    }

    // Actualizar pings cada segundo
    setInterval(updatePings, 1000);
    updatePings(); // Primera actualización

    // Lógica del botón Listo
    let countdownTimer = null;
    const readyBtn = document.getElementById('ready-btn');
    const countdown = document.getElementById('countdown');

    readyBtn.addEventListener('click', () => {
      if (readyBtn.classList.contains('active')) {
        // Cancelar countdown
        readyBtn.classList.remove('active');
        countdown.textContent = '';
        if (countdownTimer) {
          clearInterval(countdownTimer);
          countdownTimer = null;
        }
      } else {
        // Iniciar countdown
        readyBtn.classList.add('active');
        let seconds = 5;

        countdown.textContent = seconds;
        countdownTimer = setInterval(() => {
          seconds--;
          countdown.textContent = seconds;

          if (seconds <= 0) {
            clearInterval(countdownTimer);
            window.location.href = 'game.php';
          }
        }, 1000);
      }
    });

    // Botones de expulsión
    document.querySelectorAll('.kick-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const playerRow = btn.closest('.player-row');
        if (playerRow) {
          playerRow.remove();
        }
      });
    });
  </script>
  <script src="../js/script.js"></script>
</body>

</html>