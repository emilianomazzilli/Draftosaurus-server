<?php ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Crear partida - Draftosaurus</title>
  <link rel="stylesheet" href="../Css/styles.css">
  <link rel="stylesheet" href="../Css/creategame.css">
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

  <div class="container create-game-page">
    <button class="back-btn" onclick="handleBackButton()" title="Volver al menú">
      <img src="../img/botonatras.png" alt="Volver al menú" class="back-btn-img">
    </button>

    <div class="title-banner" aria-hidden="true">Crear partida</div>

    <form id="create-game-form" class="create-form" action="../Vista/lobby.php" method="post" novalidate>
      <div class="panel pergamino">
        <label for="room-name" class="field-label">Nombre de la sala</label>
        <input id="room-name" name="room_name" type="text" class="field-input" required maxlength="60" placeholder="Ej: Sala Amigos">

        <label for="room-password" class="field-label">Contraseña de la sala</label>
        <!-- ahora es un input normal y editable -->
        <input id="room-password" name="password" type="password" class="field-input" placeholder="Opcional">

        <div class="row private-row">
          <div class="private-left" style="align-items:center;">
            <label for="private-switch-btn" class="field-label" style="margin-right:8px;">Partida privada</label>
            <!-- switch solo visual -->
            <button type="button" id="private-switch-btn" class="switch-visual" role="switch" aria-checked="false" title="Partida privada (visual)"></button>
          </div>

          <button type="button" id="info-btn" class="info-btn" aria-expanded="false" aria-controls="info-overlay">i</button>
        </div>

        <label for="num-players" class="field-label">Nº de Jugadores</label>
        <div class="number-control" aria-label="Número de jugadores">
          <button type="button" id="dec-players" class="arrow-btn" aria-label="Disminuir">◀</button>
          <input id="num-players" name="players" type="number" value="2" min="2" max="5" readonly class="field-input number-input" />
          <button type="button" id="inc-players" class="arrow-btn" aria-label="Aumentar">▶</button>
        </div>
      </div>

      <!-- botón fuera del panel pero dentro del formulario -->
      <div class="form-actions">
        <button href="lobby.php" type="submit" class="btn aceptar big-aceptar">Aceptar</button>
      </div>
    </form>
  </div>

  <!-- Overlay de información (editable por vos) -->
  <div id="info-overlay" class="info-overlay" role="dialog" aria-modal="true" aria-labelledby="info-title" style="display:none;">
    <div class="info-modal pergamino">
      <button class="close-info" aria-label="Cerrar" onclick="closeInfo()">×</button>
      <h2 id="info-title">Información de la partida</h2>
      <div class="info-body">
        <p>
          Al Seleccionar “Partida Privada”
            la sala NO aparecera en la lista
            de servidores públicos y SOLO
            se podrá entrar por código
        </p>
      </div>
    </div>
  </div>

  <audio id="volver-audio" src="../Audio/sonidoatras.mp3"></audio>

  <script>
    (function() {
      // SWITCH visual (no envía valor)
      const switchBtn = document.getElementById('private-switch-btn');
      if (switchBtn) {
        switchBtn.addEventListener('click', () => {
          const checked = switchBtn.getAttribute('aria-checked') === 'true';
          switchBtn.setAttribute('aria-checked', (!checked).toString());
          switchBtn.classList.toggle('on', !checked);
        });
      }

      // Control numérico jugadores con wrap entre 2 y 5
      const dec = document.getElementById('dec-players');
      const inc = document.getElementById('inc-players');
      const numInput = document.getElementById('num-players');
      const MIN = 2,
        MAX = 5;

      function setPlayers(n) {
        let v = parseInt(n, 10) || MIN;
        if (v < MIN) v = MIN;
        if (v > MAX) v = MAX;
        numInput.value = v;
      }

      dec?.addEventListener('click', () => {
        let v = parseInt(numInput.value, 10) || MIN;
        if (v <= MIN) v = MAX;
        else v--;
        setPlayers(v);
      });

      inc?.addEventListener('click', () => {
        let v = parseInt(numInput.value, 10) || MIN;
        if (v >= MAX) v = MIN;
        else v++;
        setPlayers(v);
      });

      // Soporte teclado +/- sobre el input (para accesibilidad)
      numInput?.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft' || e.key === 'ArrowDown') {
          e.preventDefault();
          dec.click();
        } else if (e.key === 'ArrowRight' || e.key === 'ArrowUp') {
          e.preventDefault();
          inc.click();
        }
      });

      // Info overlay (mantengo comportamiento previo)
      const infoBtn = document.getElementById('info-btn');
      const infoOverlay = document.getElementById('info-overlay');
      window.closeInfo = function() {
        if (infoOverlay) infoOverlay.style.display = 'none';
        if (infoBtn) infoBtn.setAttribute('aria-expanded', 'false');
      };
      infoBtn?.addEventListener('click', function() {
        if (!infoOverlay) return;
        const visible = infoOverlay.style.display === 'flex' || infoOverlay.style.display === 'block';
        if (visible) closeInfo();
        else {
          infoOverlay.style.display = 'flex';
          infoBtn.setAttribute('aria-expanded', 'true');
        }
      });
      infoOverlay?.addEventListener('click', function(e) {
        if (e.target === infoOverlay) closeInfo();
      });
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && infoOverlay && infoOverlay.style.display !== 'none') closeInfo();
      });
    })();

    function handleBackButton() {
      // Reproduce el sonido
      const audio = document.getElementById('volver-audio');
      if (audio) {
        audio.currentTime = 0; // Reinicia el audio
        audio.play().catch(err => {
          console.log('Error al reproducir el audio:', err);
        });
      }

      // Redirige al menú después de que termine el sonido
      setTimeout(() => {
        window.location.href = 'menu.php';
      }, 1000); // Ajusta el tiempo según la duración del audio
    }
  </script>

  <script src="../js/script.js"></script>
</body>

</html>