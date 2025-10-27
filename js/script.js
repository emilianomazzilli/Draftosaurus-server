// ================= Fortaleza de contraseña (login.php y agregar.php) =================
// Bloque de UI: calcula y muestra la “fuerza” de la contraseña según longitud y tipos de caracteres.
const passwordInput = document.getElementById("password");
const strengthBar = document.getElementById("strengthBar");
const strengthText = document.getElementById("strengthText");

if (passwordInput && strengthBar && strengthText) {
  passwordInput.addEventListener("input", () => {
    const password = passwordInput.value;
    let strength = 0;

    // Reglas de fuerza: longitud, mayúscula, número y símbolo.
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    // Actualiza barra y texto según fuerza.
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
        strengthBar.style.backgroundColor = "#14e814ff";
        strengthText.style.color = "#14e814ff";
        strengthText.textContent = "Segura";
        break;
      case 4:
        strengthBar.style.width = "100%";
        strengthBar.style.backgroundColor = "#128108ff";
        strengthText.style.color = "#128108ff";
        strengthText.textContent = "Muy segura";
        break;
    }
  });
}

// ================================ AJAX formularios ================================
// Bloque principal al cargar el DOM: tema oscuro, envío AJAX de formularios, overlays y audio.
document.addEventListener("DOMContentLoaded", () => {
  // ============= INICIALIZAR TEMA (MODO OSCURO) =============
  // Lee preferencia de tema y ajusta variable CSS y checkbox.
  const savedDarkMode = localStorage.getItem("darkMode");
  const darkModeCheckbox = document.getElementById("darkMode");
  const isDarkMode = savedDarkMode === "true";

  if (isDarkMode) {
    document.documentElement.style.setProperty(
      "--bg-image",
      'url("../img/fondoOscuro.png")'
    );
    if (darkModeCheckbox) darkModeCheckbox.checked = true;
  } else {
    document.documentElement.style.setProperty(
      "--bg-image",
      'url("../img/fondo.png")'
    );
    if (darkModeCheckbox) darkModeCheckbox.checked = false;
  }

  // Envío AJAX para todos los formularios con clase .formulario (login, registro, etc.).
  const forms = document.querySelectorAll("form.formulario");

  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();

      const data = new FormData(form);

      // Petición POST; maneja redirecciones o texto de respuesta para mostrar errores.
      fetch(form.action, {
        method: "POST",
        body: data,
        credentials: "same-origin",
        redirect: "follow",
        headers: { "X-Requested-With": "XMLHttpRequest" },
      })
        .then((response) => {
          if (response.redirected) {
            window.location.replace(response.url);
            return null;
          }
          return response.text();
        })
        .then((text) => {
          if (text === null) return;

          const payload = String(text || "").trim();

          // Casos OK conocidos según endpoint.
          if (payload === "OK") {
            if (form.action.includes("process_login.php")) {
              const target = new URL("../Vista/menu.php", window.location.href)
                .href;
              window.location.replace(target);
              return;
            }
            if (form.action.includes("agregar.php")) {
              const target = new URL(
                "../Vista/login.php?ok=1",
                window.location.href
              ).href;
              window.location.replace(target);
              return;
            }
          }

          // Si devuelve HTML completo, como fallback va al menú.
          if (payload.startsWith("<!DOCTYPE") || payload.startsWith("<html")) {
            const fallback = new URL("../Vista/menu.php", window.location.href)
              .href;
            window.location.replace(fallback);
            return;
          }

          // Muestra el error devuelto por el backend.
          showFormError(form, payload || "Ocurrió un error.");
        })
        .catch(() => showFormError(form, "Error de conexión."));
    });
  });

  // Utilidad para pintar error cerca del botón submit.
  function showFormError(form, msg) {
    let errorDiv = form.querySelector(".error-message");
    if (!errorDiv) {
      errorDiv = document.createElement("div");
      errorDiv.className = "error-message";
      const submitBtn =
        form.querySelector('button[type="submit"]') || form.lastElementChild;
      form.insertBefore(errorDiv, submitBtn);
    }
    errorDiv.textContent = msg || "Error.";
  }

  // ================================ Juego / Overlays ================================
  // Helper simple: muestra overlay de puntaje si existe.
  window.mostrarOverlay = function () {
    const o = document.getElementById("puntaje-overlay");
    if (o) o.style.display = "flex";
  };

  // Perfil
  // Abre el overlay de perfil y dispara la carga de datos/foto.
  const perfilBtn = document.getElementById("perfil-btn");
  const perfilOverlay = document.getElementById("perfil-overlay");
  if (perfilBtn && perfilOverlay) {
    perfilBtn.onclick = () => {
      perfilOverlay.style.display = "flex";
      window.cargarPerfil?.(); // usar la función global para evitar referencia no definida
    };
    // Asegura que inicie oculto.
    perfilOverlay.style.display = "none";
  }

  // Cierra el overlay de perfil.
  window.cerrarPerfilOverlay = function () {
    if (perfilOverlay) perfilOverlay.style.display = "none";
  };

  // Carga datos del usuario (nombre/email) y foto; actualiza el modal y el ícono del botón.
  function cargarPerfil() {
    fetch("../Controlador/get_user.php?ts=" + Date.now(), {
      credentials: "same-origin",
      cache: "no-store",
    })
      .then((res) => {
        if (!res.ok) throw new Error("No autorizado / no encontrado");
        return res.json();
      })
      .then((data) => {
        const raw = data?.nombre ?? data?.username ?? data?.usuario ?? "";
        const nombre = raw && raw.trim() !== "" ? raw : "Usuario";

        const perfilNombre = document.getElementById("perfil-username");
        if (perfilNombre) perfilNombre.textContent = nombre;

        // Muestra email si viene del backend.
        const perfilEmail = document.getElementById("perfil-email");
        if (perfilEmail) {
          perfilEmail.textContent = data?.email ?? "";
          if (!data?.email) perfilEmail.textContent = "";
        }

        // Si hay foto, la aplica con “cache-bust” para evitar caché.
        if (data?.foto) {
          const bust = "?" + Date.now(); // cache-bust al cargar también
          const foto1 = document.getElementById("perfil-foto");
          const foto2 = document.querySelector(".perfil-icon-img");
          if (foto1) {
            foto1.onerror = () => (foto1.src = "../img/perfil.png");
            foto1.src = data.foto + bust;
          }
          if (foto2) {
            foto2.onerror = () => (foto2.src = "../img/perfil.png");
            foto2.src = data.foto + bust;
          }
        }
      })
      .catch((err) => {
        console.error("No se pudo cargar el perfil:", err);
      });
  }

  // Expone cargarPerfil globalmente y la ejecuta al cargar para reflejar la foto en el botón.
  window.cargarPerfil = cargarPerfil;
  cargarPerfil();

  // Cambiar foto (sube al servidor y refresca imágenes)
  // Referencias a elementos del formulario de foto de perfil.
  const fotoForm = document.getElementById("perfil-foto-form");
  const fotoInput = document.getElementById("foto-input");
  const perfilFotoBtn = document.querySelector(".perfil-foto-btn");
  const fotoErrorEl = document.getElementById("perfil-foto-error"); // opcional en HTML

  // Helper para mostrar errores de la sección de foto.
  function setFotoError(msg) {
    if (fotoErrorEl) fotoErrorEl.textContent = msg || "";
    else if (msg) alert(msg);
  }

  if (fotoForm && fotoInput) {
    // Validación rápida del archivo y auto-submit al elegir imagen.
    fotoInput.addEventListener("change", () => {
      setFotoError("");
      const file = fotoInput.files && fotoInput.files[0];
      if (!file) return;
      const allowed = [
        "image/jpeg",
        "image/png",
        "image/webp",
        "image/gif",
        "image/avif",
      ];
      if (!allowed.includes(file.type))
        return setFotoError("Formatos permitidos: PNG, JPEG, JPG, WebP, AVIF.");
      if (file.size > 3 * 1024 * 1024)
        return setFotoError("La imagen no debe superar 3MB.");
      // Auto submit del form.
      if (typeof fotoForm.requestSubmit === "function")
        fotoForm.requestSubmit();
      else fotoForm.dispatchEvent(new Event("submit", { cancelable: true }));
    });

    // Submit del formulario: hace POST al backend, y si OK actualiza imágenes (modal y botón).
    fotoForm.addEventListener("submit", (e) => {
      e.preventDefault();
      setFotoError("");
      const file = fotoInput.files && fotoInput.files[0];
      if (!file) return setFotoError("Selecciona una imagen.");

      const fd = new FormData();
      fd.append("foto", file);

      const origText = perfilFotoBtn ? perfilFotoBtn.textContent : null;
      if (perfilFotoBtn) {
        perfilFotoBtn.disabled = true;
        perfilFotoBtn.textContent = "Subiendo...";
      }

      fetch("../Controlador/perfil_foto.php", {
        method: "POST",
        body: fd,
        credentials: "same-origin",
        cache: "no-store",
      })
        .then((r) => r.text())
        .then((txt) => {
          if (perfilFotoBtn) {
            perfilFotoBtn.disabled = false;
            perfilFotoBtn.textContent = origText;
          }
          if (txt && txt.startsWith("../img/")) {
            // Actualiza ambas imágenes y limpia errores/archivo seleccionado.
            const bust = "?" + Date.now();
            const pf = document.getElementById("perfil-foto");
            const pi = document.querySelector(".perfil-icon-img");
            if (pf) pf.src = txt + bust;
            if (pi) pi.src = txt + bust;
            setFotoError("");
            fotoInput.value = "";
          } else {
            setFotoError(txt || "Error al subir la imagen.");
          }
        })
        .catch(() => {
          if (perfilFotoBtn) {
            perfilFotoBtn.disabled = false;
            perfilFotoBtn.textContent = origText;
          }
          setFotoError("Error de conexión. Inténtalo de nuevo.");
        });
    });
  }

  // Opciones overlay
  // Abre/cierra el overlay de Opciones (centra y muestra el modal).
  const opcionesBtn = document.getElementById("opciones-btn");
  const opcionesOverlay = document.getElementById("opciones-overlay");
  if (opcionesBtn && opcionesOverlay) {
    opcionesBtn.onclick = () => (opcionesOverlay.style.display = "flex");
    window.cerrarOpcionesOverlay = function () {
      opcionesOverlay.style.display = "none";
    };
  }

  // Sonido y música
  // Helpers que actualizan valores visibles y el volumen real del audio de fondo.
  window.actualizarSonido = function (val) {
    const el = document.getElementById("sonido-valor");
    if (el) el.textContent = val;
  };
  window.actualizarMusica = function (val) {
    const el = document.getElementById("musica-valor");
    const audio = document.getElementById("bg-music");
    if (el) el.textContent = val;
    if (audio) audio.volume = val / 100;
  };

  // Inicializa el volumen del audio y fuerza el play tras primera interacción (autoplay policy).
  const musicaRange = document.getElementById("musica-range");
  const audio = document.getElementById("bg-music");
  if (audio && musicaRange) {
    audio.volume = musicaRange.value / 100;
    audio.muted = false;
    document.body.addEventListener(
      "click",
      () => {
        audio.play().catch(() => {});
        audio.muted = false;
      },
      { once: true }
    );
  }

  // ================================ Drag & Drop seguro (juego) ================================
  // Habilita arrastrar dinos desde la mesa a casillas del tablero (interacción simple).
  const tablero = document.getElementById("tablero-casillas");
  if (tablero) tablero.style.pointerEvents = "auto";

  // Hace arrastrables los dinos de la mesa (fuente).
  document.querySelectorAll(".dino-table .dino").forEach((dino) => {
    dino.addEventListener("dragstart", (e) => {
      e.dataTransfer.setData(
        "text/dino-type",
        dino.getAttribute("data-dino") || ""
      );
      e.dataTransfer.setData("text/dino-char", dino.textContent || "");
    });
  });

  // Permite soltar un dino en una casilla si está libre y marca visualmente dragover.
  document.querySelectorAll(".casilla").forEach((casilla) => {
    casilla.addEventListener("dragover", (e) => {
      e.preventDefault();
      casilla.classList.add("over");
    });
    casilla.addEventListener("dragleave", () => {
      casilla.classList.remove("over");
    });
    casilla.addEventListener("drop", (e) => {
      e.preventDefault();
      casilla.classList.remove("over");

      if (casilla.querySelector(".dino")) return;

      const tipo = e.dataTransfer.getData("text/dino-type");
      const char = e.dataTransfer.getData("text/dino-char") || "🦖";

      const d = document.createElement("div");
      d.className = "dino";
      d.setAttribute("data-dino", tipo);
      d.setAttribute("draggable", "false");
      d.textContent = char;
      casilla.appendChild(d);

      // Atenúa el dino de la mesa correspondiente como “usado”.
      if (tipo) {
        const mesaDino = document.querySelector(
          `.dino-table .dino[data-dino="${tipo}"]`
        );
        if (mesaDino) mesaDino.style.opacity = 0.3;
      }
    });
  });

  // ============= MANEJADOR DEL MODO OSCURO =============
  // Alterna el fondo, persiste en localStorage y notifica al backend.
  document.getElementById("darkMode")?.addEventListener("change", function (e) {
    const isDarkMode = e.target.checked;

    if (isDarkMode) {
      document.documentElement.style.setProperty(
        "--bg-image",
        'url("../img/fondoOscuro.png")'
      );
    } else {
      document.documentElement.style.setProperty(
        "--bg-image",
        'url("../img/fondo.png")'
      );
    }

    // Persistir preferencia del tema.
    localStorage.setItem("darkMode", isDarkMode);

    // Aviso al backend (opcional).
    fetch("../Controlador/save_theme.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `darkMode=${isDarkMode ? 1 : 0}`,
    });
  });
});

// ----------------------------
//   Background audio persistence
// ----------------------------
// IIFE: guarda/restaura tiempo, volumen y pausa del audio de fondo; sincroniza entre pestañas.
(function () {
  const TIME_KEY = "bg_audio_time";
  const VOLUME_KEY = "bg_audio_volume";
  const PAUSED_KEY = "bg_audio_paused";
  const SAVE_INTERVAL = 1000;

  function initBackgroundAudio() {
    const audio = document.getElementById("bg-music");
    if (!audio) return;

    // Restaura volumen.
    const storedVol = localStorage.getItem(VOLUME_KEY);
    if (storedVol !== null) {
      const v = parseFloat(storedVol);
      if (!Number.isNaN(v)) audio.volume = Math.max(0, Math.min(1, v));
    }

    // Restaura estado de pausa y tiempo.
    const storedPaused = localStorage.getItem(PAUSED_KEY) === "1";
    const storedTime = parseFloat(localStorage.getItem(TIME_KEY)) || 0;

    function applyTime() {
      try {
        if (audio.duration && storedTime > 0 && storedTime < audio.duration) {
          audio.currentTime = storedTime;
        }
      } catch {}
      if (!storedPaused) {
        audio.play().catch(() => {});
      } else {
        audio.pause();
      }
    }

    // Aplica restauración cuando el metadata esté listo.
    if (audio.readyState >= 1) applyTime();
    else audio.addEventListener("loadedmetadata", applyTime, { once: true });

    // Guarda el tiempo de reproducción periódicamente.
    let lastSaved = 0;
    audio.addEventListener("timeupdate", () => {
      const now = Date.now();
      if (now - lastSaved > SAVE_INTERVAL) {
        try {
          localStorage.setItem(
            TIME_KEY,
            String(Math.floor(audio.currentTime * 100) / 100)
          );
        } catch {}
        lastSaved = now;
      }
    });

    // Guarda tiempo al salir de la página.
    window.addEventListener("beforeunload", () => {
      try {
        localStorage.setItem(
          TIME_KEY,
          String(Math.floor(audio.currentTime * 100) / 100)
        );
      } catch {}
    });

    // Persiste cambios de volumen/pausa.
    audio.addEventListener("volumechange", () => {
      try {
        localStorage.setItem(VOLUME_KEY, String(audio.volume));
      } catch {}
    });

    audio.addEventListener("play", () => {
      try {
        localStorage.setItem(PAUSED_KEY, "0");
      } catch {}
    });
    audio.addEventListener("pause", () => {
      try {
        localStorage.setItem(PAUSED_KEY, "1");
      } catch {}
    });

    // Sincroniza estado entre pestañas con el evento storage.
    window.addEventListener("storage", (e) => {
      if (!e.key) return;
      if (e.key === VOLUME_KEY && e.newValue !== null) {
        const v = parseFloat(e.newValue);
        if (!Number.isNaN(v)) audio.volume = Math.max(0, Math.min(1, v));
      }
      if (e.key === TIME_KEY && e.newValue !== null) {
        const t = parseFloat(e.newValue);
        if (!Number.isNaN(t)) {
          try {
            audio.currentTime = t;
          } catch {}
        }
      }
      if (e.key === PAUSED_KEY && e.newValue !== null) {
        if (e.newValue === "1") audio.pause();
        else audio.play().catch(() => {});
      }
    });

    // Primer click del usuario: intenta reproducir si no está marcado como pausado.
    document.addEventListener(
      "click",
      () => {
        if (audio.paused && localStorage.getItem(PAUSED_KEY) !== "1") {
          audio.play().catch(() => {});
        }
      },
      { once: true }
    );
  }

  document.addEventListener("DOMContentLoaded", initBackgroundAudio);
})();

// ===== Código añadido: abrir/cerrar overlay "buscar código", toggle contraseña y submit =====
// Maneja el overlay para ingresar un código, el toggle de visibilidad del input y la redirección.
document.addEventListener("DOMContentLoaded", () => {
  const buscarBtn = document.getElementById("buscar-codigo-btn");
  const codigoOverlay = document.getElementById("codigo-overlay");
  const cerrarBtn = document.querySelector(".close-codigo");
  const codigoForm = document.getElementById("codigo-form");
  const codigoInput = document.getElementById("codigo-input");
  const toggleBtn = document.getElementById("toggle-password");

  function abrirCodigoOverlay() {
    if (codigoOverlay) {
      codigoOverlay.style.display = "flex";
      // Pequeño delay para enfocar el input tras mostrarse.
      setTimeout(() => codigoInput?.focus(), 50);
    }
  }

  // Cierra overlay de código.
  window.cerrarCodigoOverlay = function () {
    if (codigoOverlay) codigoOverlay.style.display = "none";
  };

  // Abre overlay al click en el botón de buscar.
  if (buscarBtn) {
    buscarBtn.addEventListener("click", (e) => {
      e.preventDefault();
      abrirCodigoOverlay();
    });
  }

  // Cierra al click en el fondo.
  if (codigoOverlay) {
    codigoOverlay.addEventListener("click", (e) => {
      if (e.target === codigoOverlay) window.cerrarCodigoOverlay();
    });
  }

  // Cierra con el botón de cerrar.
  if (cerrarBtn) {
    cerrarBtn.addEventListener("click", () => window.cerrarCodigoOverlay());
  }

  // Toggle mostrar/ocultar contraseña.
  if (toggleBtn && codigoInput) {
    toggleBtn.addEventListener("click", () => {
      codigoInput.type = codigoInput.type === "password" ? "text" : "password";
      toggleBtn.setAttribute(
        "aria-pressed",
        String(codigoInput.type === "text")
      );
    });
  }

  // Enviar código: redirige a lobby.php con ?code=...
  if (codigoForm && codigoInput) {
    codigoForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const code = codigoInput.value.trim();
      if (!code) return;
      window.location.href = `lobby.php?code=${encodeURIComponent(code)}`;
    });
  }
});

//Modo Herramienta
// Datos del juego (tipos y zonas), estado y funciones de render/drag-drop.
const DINO_TYPES = [
  { id: 1, name: "T-Rex", img: "../img/dino1.png", points: 10 },
  {
    id: 2,
    name: "Triceratops",
    img: "../img/dino2.png",
    points: 1,
  },
  {
    id: 3,
    name: "Velociraptor",
    img: "../img/dino3.png",
    points: 1,
  },
  {
    id: 4,
    name: "Brachiosaurus",
    img: "../img/dino4.png",
    points: 1,
  },
  {
    id: 5,
    name: "Pterodactyl",
    img: "../img/dino5.png",
    points: 1,
  },
  {
    id: 6,
    name: "Stegosaurus",
    img: "../img/dino6.png",
    points: 1,
  },
];

const ZONES = [
  {
    id: 1,
    name: "Bosque de la Semejanza",
    left: "4%",
    top: "5%",
    width: "34%",
    height: "23%",
  },
  {
    id: 2,
    name: "El trio fondoso",
    left: "6%",
    top: "37%",
    width: "24%",
    height: "23%",
  },
  {
    id: 3,
    name: "La pradera del amor",
    left: "10%",
    top: "67%",
    width: "25%",
    height: "27%",
  },
  {
    id: 4,
    name: "El Rey de la Selva",
    left: "69%",
    top: "9%",
    width: "15%",
    height: "13%",
  },
  {
    id: 5,
    name: "El Prado de la Diferencia",
    left: "60%",
    top: "40%",
    width: "34%",
    height: "23%",
  },
  {
    id: 6,
    name: "La Isla Solitaria",
    left: "74%",
    top: "67%",
    width: "22%",
    height: "20%",
  },
  {
    id: 7,
    name: "El Rio",
    left: "47%",
    top: "80%",
    width: "21%",
    height: "%",
  },
];

// Estado del tablero (dinos por zona) y drag actual.
let zones = {};
ZONES.forEach((zone) => (zones[zone.id] = []));
let draggedDino = null;
let draggedFromZone = null;

// Inicialización del modo herramienta: renderiza lista, inventario y zonas.
function init() {
  renderDinoList();
  renderInventory();
  renderZones();
}

// Renderiza la lista de dinos arrastrables (fuente).
function renderDinoList() {
  const container = document.getElementById("dino-list");
  container.innerHTML = "";

  DINO_TYPES.forEach((dino) => {
    const item = document.createElement("div");
    item.className = `dino-item ${dino.color}`;
    item.draggable = true;
    item.innerHTML = `
                    <span><img src="${dino.img}" alt="${dino.name}" class="dino-img"></span>
                    <span class="name">${dino.name}</span>
                `;

    item.addEventListener("dragstart", (e) => {
      draggedDino = dino;
      draggedFromZone = null;
      e.dataTransfer.effectAllowed = "move";
      item.style.opacity = "0.5";
    });

    item.addEventListener("dragend", (e) => {
      item.style.opacity = "1";
    });

    container.appendChild(item);
  });
}

// Render del inventario: cuenta totales por tipo.
function renderInventory() {
  const container = document.getElementById("inventory-grid");
  container.innerHTML = "";

  DINO_TYPES.forEach((dino) => {
    const count = getTotalByType(dino.id);
    const item = document.createElement("div");
    item.className = `inventory-item ${dino.color}`;
    item.innerHTML = `
                    <div><img src="${dino.img}" alt="${dino.name}" class="dino-img"></div>
                    <div class="name">${dino.name}</div>
                    <div class="count">${count}</div>
                `;
    container.appendChild(item);
  });
}

// Render de zonas con eventos de drag-over/drop y contadores.
function renderZones() {
  const container = document.getElementById("zones-container");
  container.innerHTML = "";

  ZONES.forEach((zone) => {
    const zoneEl = document.createElement("div");
    zoneEl.className = "zone";
    zoneEl.style.left = zone.left;
    zoneEl.style.top = zone.top;
    zoneEl.style.width = zone.width;
    zoneEl.style.height = zone.height;
    zoneEl.dataset.zoneId = zone.id;

    zoneEl.innerHTML = `
                    <div class="zone-label">${zone.name}</div>
                    <div class="zone-dinos" data-zone="${zone.id}"></div>
                `;

    zoneEl.addEventListener("dragover", (e) => {
      e.preventDefault();
      e.dataTransfer.dropEffect = "move";
      zoneEl.classList.add("drag-over");
    });

    zoneEl.addEventListener("dragleave", (e) => {
      if (e.target === zoneEl) {
        zoneEl.classList.remove("drag-over");
      }
    });

    zoneEl.addEventListener("drop", (e) => {
      e.preventDefault();
      zoneEl.classList.remove("drag-over");
      handleDrop(zone.id);
    });

    container.appendChild(zoneEl);
    renderZoneDinos(zone.id);
    renderZoneSummary(zone.id);
  });
}

// Renderiza los dinos dentro de una zona (con botón eliminar).
function renderZoneDinos(zoneId) {
  const container = document.querySelector(
    `.zone-dinos[data-zone="${zoneId}"]`
  );
  if (!container) return;
  container.innerHTML = "";

  zones[zoneId].forEach((dino) => {
    const dinoEl = document.createElement("div");
    dinoEl.className = `zone-dino ${dino.color}`;
    dinoEl.draggable = true;

    // Solo imagen del dino.
    const imgHtml = dino.img
      ? `<img src="${dino.img}" alt="${dino.name}" class="zone-dino-img">`
      : `<div class="zone-dino-placeholder"></div>`;

    dinoEl.innerHTML = `
      ${imgHtml}
      <button class="delete-btn" aria-label="Eliminar">×</button>
    `;

    // Permite re-arrastrar desde la zona actual.
    dinoEl.addEventListener("dragstart", (e) => {
      draggedDino = dino;
      draggedFromZone = zoneId;
      e.dataTransfer.effectAllowed = "move";
      dinoEl.style.opacity = "0.5";
      e.stopPropagation();
    });

    dinoEl.addEventListener("dragend", () => {
      dinoEl.style.opacity = "1";
    });

    // Eliminar dino de la zona.
    const deleteBtn = dinoEl.querySelector(".delete-btn");
    deleteBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      removeDinosaur(zoneId, dino.uniqueId);
    });

    container.appendChild(dinoEl);
  });
}

// Muestra el resumen de puntaje por zona según sus reglas.
function renderZoneSummary(zoneId) {
  const container = document.querySelector(
    `.zone-summary[data-zone="${zoneId}"]`
  );
  if (!container) return;

  // Puntaje por zona
  const points = getZoneScore(zoneId);

  let extra = "";
  if (zoneId === 1) {
    const arr = zones[1];
    if (arr.length > 0) {
      const lockedTypeId = arr[0].id;
      const d = DINO_TYPES.find((x) => x.id === lockedTypeId);
      extra = d ? ` — Tipo fijo: ${d.emoji} ${d.name}` : "";
    }
  }
  container.textContent = `Puntos: ${points}${extra}`;
}

// Regla de admisión para cada zona (quién puede entrar).
function canDropInZone(zoneId, dino) {
  if (zoneId === 1) {
    const arr = zones[1];
    if (arr.length === 0) return true;
    const lockedTypeId = arr[0].id; // todos deben ser del mismo tipo que el primero
    return dino.id === lockedTypeId;
  }
  // Zona 4: solo 1 dino permitido
  if (zoneId === 4) {
    return zones[4].length === 0;
  }
  // Zonas 2 y 3: sin restricción de admisión (solo puntaje cambia).
  return true;
}

// Cálculo de puntaje por zona (reglas específicas).
function getZoneScore(zoneId) {
  const arr = zones[zoneId] || [];
  const n = arr.length;

  if (zoneId === 1) {
    // tabla: 1→2, 2→4, 3→8, 4→12, 5→18, 6→24 (y extensión suave)
    const table = { 1: 2, 2: 4, 3: 8, 4: 12, 5: 18, 6: 24 };
    return table[n] ?? (n > 6 ? 24 + (n - 6) * 4 : 0);
  }

  if (zoneId === 2) {
    return Math.floor(n / 3) * 7;
  }

  if (zoneId === 3) {
    // parejas iguales: 5 puntos por pareja del mismo tipo
    let pairs = 0;
    for (const d of DINO_TYPES) {
      const c = arr.filter((x) => x.id === d.id).length;
      pairs += Math.floor(c / 2);
    }
    return pairs * 5;
  }

  // Zona 4: 7 puntos si hay exactamente 1 dino
  if (zoneId === 4) {
    return n === 1 ? 7 : 0;
  }

  if( zoneId === 5) {

  }
  if( zoneId === 6) {

  }
  if( zoneId === 7) {
    
  }
  return 0;
}

// Maneja el drop: valida, mueve/crea la ficha y re-renderiza vistas relacionadas.
function handleDrop(zoneId) {
  if (!draggedDino) return;

  // Valida admisión según la zona.
  const canDrop = canDropInZone(zoneId, draggedDino);
  if (!canDrop) {
    // Feedback visual rápido si no se permite.
    const z = document.querySelector(`.zone[data-zone-id="${zoneId}"]`);
    if (z && z.animate)
      z.animate(
        [
          { transform: "translateX(0)" },
          { transform: "translateX(-4px)" },
          { transform: "translateX(4px)" },
          { transform: "translateX(0)" },
        ],
        { duration: 150 }
      );
    return;
  }

  // Reubica o crea un nuevo dino con uniqueId.
  if (draggedFromZone !== null && draggedFromZone !== zoneId) {
    zones[draggedFromZone] = zones[draggedFromZone].filter(
      (d) => d.uniqueId !== draggedDino.uniqueId
    );
    zones[zoneId].push(draggedDino);
  } else if (draggedFromZone === null) {
    const newDino = { ...draggedDino, uniqueId: Date.now() + Math.random() };
    zones[zoneId].push(newDino);
  }

  // Limpia estado de drag y actualiza UI relacionada.
  draggedDino = null;
  draggedFromZone = null;
  renderInventory();
  renderZones();
  renderScorePanel();
}

// Elimina un dino por uniqueId de una zona y refresca vistas.
function removeDinosaur(zoneId, uniqueId) {
  zones[zoneId] = zones[zoneId].filter((d) => d.uniqueId !== uniqueId);
  renderInventory();
  renderZones();
  renderScorePanel();
}

// Helpers de conteo por zona o global por tipo.
function getDinoCount(zoneId, dinoId) {
  return zones[zoneId].filter((d) => d.id === dinoId).length;
}

function getTotalByType(dinoId) {
  return Object.values(zones)
    .flat()
    .filter((d) => d.id === dinoId).length;
}

// Arranque del modo herramienta.
init();

// Overlay informativo: carrusel de slides (abrir, cerrar, navegar)
// Muestra un carrusel con varias pantallas informativas; controles prev/next/close e indicadores.
(function () {
  const overlay = document.getElementById("tool-overlay");
  const openOnLoad = true; // si quieres que abra al cargar
  if (!overlay) return;

  const track = overlay.querySelector(".tool-carousel-track");
  const slides = Array.from(overlay.querySelectorAll(".tool-slide"));
  const prevBtn = document.getElementById("tool-prev");
  const nextBtn = document.getElementById("tool-next");
  const closeBtn = document.getElementById("tool-close");
  const infoBtn = document.getElementById("parque-info-btn");
  const indicators = overlay.querySelector(".tool-indicators");

  let index = 0;

  function update() {
    if (track) track.style.transform = `translateX(-${index * 100}%)`;
    if (prevBtn) prevBtn.disabled = index === 0;
    if (nextBtn) nextBtn.disabled = index === slides.length - 1;
    updateIndicators();
  }

  function updateIndicators() {
    if (!indicators) return;
    indicators.innerHTML = "";
    slides.forEach((s, i) => {
      const dot = document.createElement("button");
      dot.className = "tool-indicator";
      dot.setAttribute("aria-label", `Slide ${i + 1}`);
      dot.disabled = i === index;
      dot.addEventListener("click", () => {
        index = i;
        update();
      });
      indicators.appendChild(dot);
    });
  }

  function openOverlay() {
    overlay.classList.remove("hidden");
    overlay.style.display = "flex";
    // Animación de entrada y foco al primer control.
    overlay.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 220, easing: "ease-out" });
    setTimeout(() => {
      (overlay.querySelector(".tool-slide[href], .tool-close, .tool-nav, .tool-indicator") || overlay).focus?.();
    }, 200);
  }

  function closeOverlay() {
    overlay.classList.add("hidden");
    overlay.style.display = "none";
    // Reinicia al primer slide al cerrar (opcional).
    index = 0;
    update();
  }

  // Eventos de navegación del carrusel.
  prevBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    if (index > 0) {
      index--;
      update();
      // Animación suave hacia la izquierda
      track?.animate([
        { transform: `translateX(-${(index + 1) * 100}%)`, offset: 0 },
        { transform: `translateX(-${index * 100}%)`, offset: 1 }
      ], {
        duration: 350,
        easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
      });
    }
  });

  nextBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    if (index < slides.length - 1) {
      index++;
      update();
      // Animación suave hacia la derecha
      track?.animate([
        { transform: `translateX(-${(index - 1) * 100}%)`, offset: 0 },
        { transform: `translateX(-${index * 100}%)`, offset: 1 }
      ], {
        duration: 350,
        easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
      });
    }
  });

  closeBtn?.addEventListener("click", (e) => {
    e.stopPropagation();
    closeOverlay();
  });

  // Botón “i” abre el overlay.
  infoBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    openOverlay();
  });

  // Cierra al click fuera del modal.
  overlay.addEventListener("click", (e) => {
    const modal = overlay.querySelector(".tool-modal");
    if (e.target === overlay) closeOverlay();
    if (modal && !modal.contains(e.target)) closeOverlay();
  });

  // Soporte de teclado: Esc cierra, flechas navegan.
  document.addEventListener("keydown", (e) => {
    if (overlay.classList.contains("hidden")) return;
    if (e.key === "Escape") {
      e.preventDefault();
      closeOverlay();
      return;
    }
    if (e.key === "ArrowLeft") {
      if (index > 0) {
        index--;
        update();
      }
    }
    if (e.key === "ArrowRight") {
      if (index < slides.length - 1) {
        index++;
        update();
      }
    }
  });

  // Inicializa ancho del track y slides; abre si openOnLoad.
  function init() {
    if (!track) return;
    track.style.width = `${slides.length * 100}%`;
    slides.forEach((s) => (s.style.width = `${100 / slides.length}%`));
    update();
    if (openOnLoad) openOverlay();
  }

  // Expone funciones globales (si otros módulos quieren abrir/cerrar).
  window.openToolOverlay = openOverlay;
  window.closeToolOverlay = closeOverlay;

  // Init en DOMContentLoaded (o de inmediato si ya cargó).
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else init();
})();

// Panel de puntajes: lista de puntos por zona (si existe el contenedor).
function renderScorePanel() {
  const ul = document.getElementById("zone-scores");
  if (!ul) return;
  ul.innerHTML = "";
  ZONES.forEach((zone) => {
    const puntos = getZoneScore(zone.id);
    const li = document.createElement("li");
    li.textContent = `${zone.name}: ${puntos} puntos`;
    ul.appendChild(li);
  });
}


// Función para el botón volver con audio
// Reproduce un sonido “volver” y, tras un breve delay, navega atrás.
function handleBackButton() {
  const volverAudio = document.getElementById('volver-audio');
  if (volverAudio) {
    volverAudio.currentTime = 0; // Reinicia el audio
    volverAudio.play().then(() => {
      setTimeout(() => {
        window.history.back(); // Vuelve atrás después de reproducir el sonido
      }, 200); // Pequeño delay para que se escuche el sonido
    }).catch(err => {
      console.log('Error reproduciendo audio:', err);
      window.history.back(); // Si hay error, vuelve atrás de todos modos
    });
  } else {
    window.history.back();
  }
}

// Perfil overlay
// Bloque de compatibilidad: vuelve a registrar handlers de perfil y código si la página lo requiere.
document.addEventListener('DOMContentLoaded', () => {
  const perfilBtn = document.getElementById('perfil-btn');
  const perfilOverlay = document.getElementById('perfil-overlay');

  if (perfilBtn && perfilOverlay) {
    perfilBtn.onclick = () => {
      perfilOverlay.style.display = 'flex';
      window.cargarPerfil?.(); // usar la función global para evitar referencia no definida
    };
  }

  window.cerrarPerfilOverlay = function() {
    if (perfilOverlay) perfilOverlay.style.display = 'none';
  };

  // Código overlay
  const buscarBtn = document.getElementById('buscar-codigo-btn');
  const codigoOverlay = document.getElementById('codigo-overlay');

  if (buscarBtn && codigoOverlay) {
    buscarBtn.onclick = (e) => {
      e.preventDefault();
      codigoOverlay.style.display = 'flex';
    };
  }

  window.cerrarCodigoOverlay = function() {
    if (codigoOverlay) codigoOverlay.style.display = 'none';
  };
});