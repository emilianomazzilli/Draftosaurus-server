// ================= Fortaleza de contraseña (login.php y agregar.php) =================
const passwordInput = document.getElementById("password");
const strengthBar = document.getElementById("strengthBar");
const strengthText = document.getElementById("strengthText");

if (passwordInput && strengthBar && strengthText) {
  passwordInput.addEventListener("input", () => {
    const password = passwordInput.value;
    let strength = 0;

    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    switch (strength) {
      case 0:
        strengthBar.style.width = "0";
        strengthText.textContent = "";
        break;
      case 1:
        strengthBar.style.width = "25%";
        strengthBar.style.backgroundColor = "#ff4d4d";
        strengthText.style.color = "#ff4d4d";
        strengthText.textContent = "Poco segura";
        break;
      case 2:
        strengthBar.style.width = "50%";
        strengthBar.style.backgroundColor = "#ff944d";
        strengthText.style.color = "#ff944d";
        strengthText.textContent = "Medio segura";
        break;
      case 3:
        strengthBar.style.width = "75%";
        strengthBar.style.backgroundColor = "#ffff66";
        strengthText.style.color = "#ffff66";
        strengthText.textContent = "Segura";
        break;
      case 4:
        strengthBar.style.width = "100%";
        strengthBar.style.backgroundColor = "#1ece1eff";
        strengthText.style.color = "#1ece1eff";
        strengthText.textContent = "Muy segura";
        break;
    }
  });
}

// ================================ AJAX formularios ================================
document.addEventListener("DOMContentLoaded", () => {
  const forms = document.querySelectorAll("form.formulario");

  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();

      const data = new FormData(form);

      fetch(form.action, {
        method: "POST",
        body: data,
        credentials: "same-origin",
        redirect: "follow",
        // IMPORTANTE: que el PHP lo reconozca como AJAX
        headers: { "X-Requested-With": "XMLHttpRequest" }
      })
        .then((response) => {
          if (response.redirected) {
            window.location.replace(response.url);
            return null;
          }
          return response.text();
        })
        .then((text) => {
          if (text === null) return; // ya redirigimos

          const payload = String(text || "").trim();

          if (payload === "OK") {
            if (form.action.includes("process_login.php")) {
              const target = new URL("../Vista/menu.php", window.location.href).href;
              window.location.replace(target);
              return;
            }
            if (form.action.includes("agregar.php")) {
              const target = new URL("../Vista/login.php?ok=1", window.location.href).href;
              window.location.replace(target);
              return;
            }
          }

          // Si recibimos HTML, fallback
          if (payload.startsWith("<!DOCTYPE") || payload.startsWith("<html")) {
            const fallback = new URL("../Vista/menu.php", window.location.href).href;
            window.location.replace(fallback);
            return;
          }

          showFormError(form, payload || "Ocurrió un error.");
        })
        .catch(() => showFormError(form, "Error de conexión."));
    });
  });

  function showFormError(form, msg) {
    let errorDiv = form.querySelector(".error-message");
    if (!errorDiv) {
      errorDiv = document.createElement("div");
      errorDiv.className = "error-message";
      const submitBtn = form.querySelector('button[type="submit"]') || form.lastElementChild;
      form.insertBefore(errorDiv, submitBtn);
    }
    errorDiv.textContent = msg || "Error.";
  }

  // ================================ Juego / Overlays ================================
  // Mostrar overlay de puntaje (si existe)
  window.mostrarOverlay = function () {
    const o = document.getElementById('puntaje-overlay');
    if (o) o.style.display = 'flex';
  };

  // Perfil
  const perfilBtn = document.getElementById('perfil-btn');
  const perfilOverlay = document.getElementById('perfil-overlay');
  if (perfilBtn && perfilOverlay) {
    perfilBtn.onclick = () => {
      perfilOverlay.style.display = 'flex';
      cargarPerfil();
    };
    // Ocultar al cargar por si viene visible
    perfilOverlay.style.display = 'none';
  }

  window.cerrarPerfilOverlay = function () {
    if (perfilOverlay) perfilOverlay.style.display = 'none';
  };

  function cargarPerfil() {
    fetch('../Controlador/get_user.php')
      .then(res => res.json())
      .then(data => {
        const nombre = data?.nombre || 'Usuario';
        const perfilNombre = document.getElementById('perfil-nombre');
        const perfilNombreTitulo = document.getElementById('perfil-nombre-titulo');
        if (perfilNombre) perfilNombre.textContent = nombre;
        if (perfilNombreTitulo) perfilNombreTitulo.textContent = nombre;
        if (data?.foto) {
          const foto1 = document.getElementById('perfil-foto');
          const foto2 = document.querySelector('.perfil-icon');
          if (foto1) foto1.src = data.foto;
          if (foto2) foto2.src = data.foto;
        }
      })
      .catch(() => {
        /* Mensaje Error */
      });
  }

  // Cambiar foto
  const fotoForm = document.getElementById('perfil-foto-form');
  if (fotoForm) {
    fotoForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const input = document.getElementById('foto-input');
      if (input?.files && input.files[0]) {
        const formData = new FormData();
        formData.append('foto', input.files[0]);
        fetch('../Controlador/perfil_foto.php', { method: 'POST', body: formData })
          .then(res => res.text())
          .then(ruta => {
            if (ruta.startsWith('../img/')) {
              const bust = '?' + Date.now();
              const pf = document.getElementById('perfil-foto');
              const pi = document.querySelector('.perfil-icon');
              if (pf) pf.src = ruta + bust;
              if (pi) pi.src = ruta + bust;
            } else {
              alert(ruta);
            }
          });
      }
    });
  }

  // Opciones overlay
  const opcionesBtn = document.getElementById('opciones-btn');
  const opcionesOverlay = document.getElementById('opciones-overlay');
  if (opcionesBtn && opcionesOverlay) {
    opcionesBtn.onclick = () => opcionesOverlay.style.display = 'flex';
    window.cerrarOpcionesOverlay = function () {
      opcionesOverlay.style.display = 'none';
    };
  }

  // Sonido y música
  window.actualizarSonido = function (val) {
    const el = document.getElementById('sonido-valor');
    if (el) el.textContent = val;
  };
  window.actualizarMusica = function (val) {
    const el = document.getElementById('musica-valor');
    const audio = document.getElementById('bg-music');
    if (el) el.textContent = val;
    if (audio) audio.volume = val / 100;
  };

  // Inicializa el volumen si hay audio
  const musicaRange = document.getElementById('musica-range');
  const audio = document.getElementById('bg-music');
  if (audio && musicaRange) {
    audio.volume = musicaRange.value / 100;
    audio.muted = false;
    document.body.addEventListener('click', () => {
      audio.play().catch(() => {/* autoplay bloqueado */});
      audio.muted = false;
    }, { once: true });
  }

  // ================================ Drag & Drop (seguro) ================================
  // Habilitar eventos en #tablero-casillas
  const tablero = document.getElementById('tablero-casillas');
  if (tablero) tablero.style.pointerEvents = 'auto';

  // Arrastrables (dinos de la mesa)
  document.querySelectorAll('.dino-table .dino').forEach(dino => {
    dino.addEventListener('dragstart', (e) => {
      // En vez de outerHTML pasamos datos simples
      e.dataTransfer.setData('text/dino-type', dino.getAttribute('data-dino') || '');
      e.dataTransfer.setData('text/dino-char', dino.textContent || '');
    });
  });

  // Drop en casillas
  document.querySelectorAll('.casilla').forEach(casilla => {
    casilla.addEventListener('dragover', (e) => {
      e.preventDefault();
      casilla.classList.add('over');
    });
    casilla.addEventListener('dragleave', () => {
      casilla.classList.remove('over');
    });
    casilla.addEventListener('drop', (e) => {
      e.preventDefault();
      casilla.classList.remove('over');

      // Solo un dino por casilla
      if (casilla.querySelector('.dino')) return;

      const tipo = e.dataTransfer.getData('text/dino-type');
      const char = e.dataTransfer.getData('text/dino-char') || '🦖';

      // Crear el nodo (sin innerHTML)
      const d = document.createElement('div');
      d.className = 'dino';
      d.setAttribute('data-dino', tipo);
      d.setAttribute('draggable', 'false');
      d.textContent = char;
      casilla.appendChild(d);

      // Deshabilitar el dino en la mesa
      if (tipo) {
        const mesaDino = document.querySelector(`.dino-table .dino[data-dino="${tipo}"]`);
        if (mesaDino) mesaDino.style.opacity = 0.3;
      }
    });
  });

});
