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
document.addEventListener("DOMContentLoaded", () => {
  // ============= INICIALIZAR TEMA (MODO OSCURO) =============
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

          if (payload.startsWith("<!DOCTYPE") || payload.startsWith("<html")) {
            const fallback = new URL("../Vista/menu.php", window.location.href)
              .href;
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
      const submitBtn =
        form.querySelector('button[type="submit"]') || form.lastElementChild;
      form.insertBefore(errorDiv, submitBtn);
    }
    errorDiv.textContent = msg || "Error.";
  }

  // ================================ Juego / Overlays ================================
  window.mostrarOverlay = function () {
    const o = document.getElementById("puntaje-overlay");
    if (o) o.style.display = "flex";
  };

  // Perfil
  const perfilBtn = document.getElementById("perfil-btn");
  const perfilOverlay = document.getElementById("perfil-overlay");
  if (perfilBtn && perfilOverlay) {
    perfilBtn.onclick = () => {
      perfilOverlay.style.display = "flex";
      cargarPerfil();
    };
    perfilOverlay.style.display = "none";
  }

  window.cerrarPerfilOverlay = function () {
    if (perfilOverlay) perfilOverlay.style.display = "none";
  };

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

        // Nuevo: mostrar email si viene
        const perfilEmail = document.getElementById("perfil-email");
        if (perfilEmail) {
          perfilEmail.textContent = data?.email ?? "";
          if (!data?.email) perfilEmail.textContent = "";
        }

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

  // Cambiar foto (sube al servidor y refresca imágenes)
  const fotoForm = document.getElementById("perfil-foto-form");
  const fotoInput = document.getElementById("foto-input");
  const perfilFotoBtn = document.querySelector(".perfil-foto-btn");
  const fotoErrorEl = document.getElementById("perfil-foto-error"); // opcional en HTML

  function setFotoError(msg) {
    if (fotoErrorEl) fotoErrorEl.textContent = msg || "";
    else if (msg) alert(msg);
  }

  if (fotoForm && fotoInput) {
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
      // Auto submit
      if (typeof fotoForm.requestSubmit === "function")
        fotoForm.requestSubmit();
      else fotoForm.dispatchEvent(new Event("submit", { cancelable: true }));
    });

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
  const opcionesBtn = document.getElementById("opciones-btn");
  const opcionesOverlay = document.getElementById("opciones-overlay");
  if (opcionesBtn && opcionesOverlay) {
    opcionesBtn.onclick = () => (opcionesOverlay.style.display = "flex");
    window.cerrarOpcionesOverlay = function () {
      opcionesOverlay.style.display = "none";
    };
  }

  // Sonido y música
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

  // Inicializa el volumen si hay audio
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
  const tablero = document.getElementById("tablero-casillas");
  if (tablero) tablero.style.pointerEvents = "auto";

  document.querySelectorAll(".dino-table .dino").forEach((dino) => {
    dino.addEventListener("dragstart", (e) => {
      e.dataTransfer.setData(
        "text/dino-type",
        dino.getAttribute("data-dino") || ""
      );
      e.dataTransfer.setData("text/dino-char", dino.textContent || "");
    });
  });

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

      if (tipo) {
        const mesaDino = document.querySelector(
          `.dino-table .dino[data-dino="${tipo}"]`
        );
        if (mesaDino) mesaDino.style.opacity = 0.3;
      }
    });
  });

  // ============= MANEJADOR DEL MODO OSCURO =============
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

    // Persistir preferencia del tema (esto sí es útil conservarlo)
    localStorage.setItem("darkMode", isDarkMode);

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
(function () {
  const TIME_KEY = "bg_audio_time";
  const VOLUME_KEY = "bg_audio_volume";
  const PAUSED_KEY = "bg_audio_paused";
  const SAVE_INTERVAL = 1000;

  function initBackgroundAudio() {
    const audio = document.getElementById("bg-music");
    if (!audio) return;

    const storedVol = localStorage.getItem(VOLUME_KEY);
    if (storedVol !== null) {
      const v = parseFloat(storedVol);
      if (!Number.isNaN(v)) audio.volume = Math.max(0, Math.min(1, v));
    }

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

    if (audio.readyState >= 1) applyTime();
    else audio.addEventListener("loadedmetadata", applyTime, { once: true });

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

    window.addEventListener("beforeunload", () => {
      try {
        localStorage.setItem(
          TIME_KEY,
          String(Math.floor(audio.currentTime * 100) / 100)
        );
      } catch {}
    });

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
      // pequeño delay para asegurar que el elemento está visible antes de focus
      setTimeout(() => codigoInput?.focus(), 50);
    }
  }

  window.cerrarCodigoOverlay = function () {
    if (codigoOverlay) codigoOverlay.style.display = "none";
  };

  if (buscarBtn) {
    buscarBtn.addEventListener("click", (e) => {
      e.preventDefault();
      abrirCodigoOverlay();
    });
  }

  // cerrar al hacer click en el fondo del overlay
  if (codigoOverlay) {
    codigoOverlay.addEventListener("click", (e) => {
      if (e.target === codigoOverlay) window.cerrarCodigoOverlay();
    });
  }

  if (cerrarBtn) {
    cerrarBtn.addEventListener("click", () => window.cerrarCodigoOverlay());
  }

  if (toggleBtn && codigoInput) {
    toggleBtn.addEventListener("click", () => {
      codigoInput.type = codigoInput.type === "password" ? "text" : "password";
      toggleBtn.setAttribute(
        "aria-pressed",
        String(codigoInput.type === "text")
      );
    });
  }

  if (codigoForm && codigoInput) {
    codigoForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const code = codigoInput.value.trim();
      if (!code) return;
      // Ajusta la acción según tu flujo: ejemplo redirige a lobby.php con el código
      window.location.href = `lobby.php?code=${encodeURIComponent(code)}`;
    });
  }
});

//Modo Herramienta

const DINO_TYPES = [
  { id: 1, name: "T-Rex", img: "/Drafto-Juego-main/img/dino1.png", points: 10 },
  {
    id: 2,
    name: "Triceratops",
    img: "/Drafto-Juego-main/img/dino2.png",
    points: 8,
  },
  {
    id: 3,
    name: "Velociraptor",
    img: "/Drafto-Juego-main/img/dino3.png",
    points: 6,
  },
  {
    id: 4,
    name: "Brachiosaurus",
    img: "/Drafto-Juego-main/img/dino4.png",
    points: 12,
  },
  {
    id: 5,
    name: "Pterodactyl",
    img: "/Drafto-Juego-main/img/dino5.png",
    points: 7,
  },
  {
    id: 6,
    name: "Stegosaurus",
    img: "/Drafto-Juego-main/img/dino6.png",
    points: 9,
  },
];

const ZONES = [
  {
    id: 1,
    name: "Bosque de la Semejanza",
    left: "5%",
    top: "5%",
    width: "38%",
    height: "28%",
  },
  {
    id: 2,
    name: "El trio fondoso",
    left: "5%",
    top: "36%",
    width: "38%",
    height: "28%",
  },
  {
    id: 3,
    name: "La pradera del amor",
    left: "5%",
    top: "67%",
    width: "38%",
    height: "28%",
  },
  {
    id: 4,
    name: "El Rey de la Selva",
    left: "57%",
    top: "5%",
    width: "38%",
    height: "28%",
  },
  {
    id: 5,
    name: "El Prado de la Diferencia",
    left: "57%",
    top: "36%",
    width: "38%",
    height: "28%",
  },
  {
    id: 6,
    name: "La Isla Solitaria",
    left: "57%",
    top: "67%",
    width: "38%",
    height: "28%",
  },
  {
    id: 7,
    name: "El Rio",
    left: "35%",
    top: "72%",
    width: "30%",
    height: "23%",
  },
];

let zones = {};
ZONES.forEach((zone) => (zones[zone.id] = []));

let draggedDino = null;
let draggedFromZone = null;

function init() {
  renderDinoList();
  renderInventory();
  renderZones();
}

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
                    <div class="zone-count">Total: ${
                      zones[zone.id].length
                    }</div>
                    <div class="zone-dinos" data-zone="${zone.id}"></div>
                    <div class="zone-summary" data-zone="${zone.id}"></div>
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

    // Mostrar SOLO la imagen (sin emoji)
    const imgHtml = dino.img
      ? `<img src="${dino.img}" alt="${dino.name}" class="zone-dino-img">`
      : `<div class="zone-dino-placeholder"></div>`;

    dinoEl.innerHTML = `
      ${imgHtml}
      <button class="delete-btn" aria-label="Eliminar">×</button>
    `;

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

    const deleteBtn = dinoEl.querySelector(".delete-btn");
    deleteBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      removeDinosaur(zoneId, dino.uniqueId);
    });

    container.appendChild(dinoEl);
  });
}

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

// Regla de admisión para cada zona
function canDropInZone(zoneId, dino) {
  if (zoneId === 1) {
    const arr = zones[1];
    if (arr.length === 0) return true;
    const lockedTypeId = arr[0].id; // al estar restringido, todos serán del mismo tipo
    return dino.id === lockedTypeId;
  }
  // Zonas 2 y 3: se puede colocar cualquier dino (la regla es de puntaje, no de admisión)
  return true;
}

// Cálculo de puntaje por zona
function getZoneScore(zoneId) {
  const arr = zones[zoneId] || [];
  const n = arr.length;

  if (zoneId === 1) {
    // tabla: 1→2, 2→4, 3→8, 4→12, 5→18, 6→24
    const table = { 1: 2, 2: 4, 3: 8, 4: 12, 5: 18, 6: 24 };
    return table[n] ?? (n > 6 ? 24 + (n - 6) * 4 : 0); // extensión suave si pasan de 6
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

  // otras zonas (sin regla aún)
  return 0;
}

function handleDrop(zoneId) {
  if (!draggedDino) return;

  // Valida la admisión antes de mover
  const canDrop = canDropInZone(zoneId, draggedDino);
  if (!canDrop) {
    // feedback suave (opcional)
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

  if (draggedFromZone !== null && draggedFromZone !== zoneId) {
    zones[draggedFromZone] = zones[draggedFromZone].filter(
      (d) => d.uniqueId !== draggedDino.uniqueId
    );
    zones[zoneId].push(draggedDino);
  } else if (draggedFromZone === null) {
    const newDino = { ...draggedDino, uniqueId: Date.now() + Math.random() };
    zones[zoneId].push(newDino);
  }

  draggedDino = null;
  draggedFromZone = null;
  renderInventory();
  renderZones();
  renderScorePanel();
}

function removeDinosaur(zoneId, uniqueId) {
  zones[zoneId] = zones[zoneId].filter((d) => d.uniqueId !== uniqueId);
  renderInventory();
  renderZones();
  renderScorePanel();
}

function getDinoCount(zoneId, dinoId) {
  return zones[zoneId].filter((d) => d.id === dinoId).length;
}

function getTotalByType(dinoId) {
  return Object.values(zones)
    .flat()
    .filter((d) => d.id === dinoId).length;
}

init();

// Overlay informativo: abrir en carga y controlar cierre (cruz, click fuera, Escape) + reabrir con botón "i"
(function () {
  const overlay = document.getElementById("tool-overlay");
  const closeBtn = document.getElementById("tool-close");
  const infoBtn = document.getElementById("parque-info-btn");

  function openOverlay() {
    overlay && overlay.classList.remove("hidden");
  }

  function closeOverlay() {
    overlay && overlay.classList.add("hidden");
  }

  document.addEventListener("DOMContentLoaded", () => {
    // Mostrar al entrar en la página
    openOverlay();
  });

  // Cerrar con la cruz
  closeBtn?.addEventListener("click", (e) => {
    e.stopPropagation();
    closeOverlay();
  });

  // Cerrar haciendo click fuera del modal
  overlay?.addEventListener("click", (e) => {
    if (e.target === overlay) closeOverlay();
  });

  // Reabrir con botón "i"
  infoBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    openOverlay();
  });

  // Cerrar con Escape
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeOverlay();
  });
})();

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
