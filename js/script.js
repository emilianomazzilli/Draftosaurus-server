// ================================
// 🌿 EFECTO HOJAS CAYENDO
// ================================
function initFallingLeaves() {
  const leavesContainer = document.getElementById("leaves");

  // Evita duplicar hojas y respeta "prefers-reduced-motion"
  if (!leavesContainer) return;
  if (window.matchMedia && matchMedia("(prefers-reduced-motion: reduce)").matches) return;
  if (leavesContainer.dataset.init === "1") return;
  leavesContainer.dataset.init = "1";

  const leafEmojis = ["🍃", "🍂", "🌿", "🍁"];
  for (let i = 0; i < 20; i++) {
    const leaf = document.createElement("div");
    leaf.className = "leaf";
    leaf.textContent = leafEmojis[Math.floor(Math.random() * leafEmojis.length)];
    leaf.style.left = Math.random() * 100 + "%";
    leaf.style.top = "-20px";
    leaf.style.animationDelay = Math.random() * 10 + "s";
    leaf.style.animationDuration = Math.random() * 10 + 10 + "s";
    leavesContainer.appendChild(leaf);
  }
}
document.addEventListener("DOMContentLoaded", initFallingLeaves);

// ================================
// 🔐 FORTALEZA DE CONTRASEÑA
// ================================
const passwordInput = document.getElementById("password");
const strengthBar = document.getElementById("strengthBar");
const strengthText = document.getElementById("strengthText");

if (passwordInput && strengthBar && strengthText) {
  passwordInput.addEventListener("input", () => {
    const password = passwordInput.value;
    let strength = 0;

    // Reglas de fuerza
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    // Feedback visual
    switch (strength) {
      case 0:
        strengthBar.style.width = "0";
        strengthBar.style.backgroundColor = "";
        strengthText.style.color = "";
        strengthText.textContent = "";
        break;
      case 1:
        strengthBar.style.width = "25%";
        strengthBar.style.backgroundColor = "rgba(255,77,77,1)";
        strengthText.style.color = "rgba(255,77,77,1)";
        strengthText.textContent = "Poco segura";
        break;
      case 2:
        strengthBar.style.width = "50%";
        strengthBar.style.backgroundColor = "rgba(255,148,77,1)";
        strengthText.style.color = "rgba(255,148,77,1)";
        strengthText.textContent = "Medio segura";
        break;
      case 3:
        strengthBar.style.width = "75%";
        strengthBar.style.backgroundColor = "rgba(20,232,20,1)";
        strengthText.style.color = "rgba(20,232,20,1)";
        strengthText.textContent = "Segura";
        break;
      case 4:
        strengthBar.style.width = "100%";
        strengthBar.style.backgroundColor = "rgba(18,129,8,1)";
        strengthText.style.color = "rgba(18,129,8,1)";
        strengthText.textContent = "Muy segura";
        break;
    }
  });
}

// ================================
// 📬 FORMULARIOS AJAX + MODO OSCURO
// ================================
document.addEventListener("DOMContentLoaded", () => {
  // ---- Inicializar fondo según preferencia guardada
  const savedDarkMode = localStorage.getItem("darkMode");
  const darkModeCheckbox = document.getElementById("darkMode");
  const isDarkMode = savedDarkMode === "true";

  document.documentElement.style.setProperty(
    "--bg-image",
    isDarkMode ? 'url("../img/fondoOscuro.png")' : 'url("../img/fondo.png")'
  );
  if (darkModeCheckbox) darkModeCheckbox.checked = isDarkMode;

  // ---- Envío AJAX para formularios con clase .formulario
  const forms = document.querySelectorAll("form.formulario");

  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      e.stopPropagation();

      const data = new FormData(form);

      fetch(form.action, {
        method: "POST",
        body: data,
        credentials: "same-origin",
        redirect: "follow",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "text/html, text/plain, application/json"
        },
      })
        .then((response) => {
          if (response.redirected) {
            window.location.replace(response.url);
            return null;
          }
          const ct = response.headers.get("content-type") || "";
          if (ct.includes("application/json")) return response.json();
          return response.text();
        })
        .then((text) => {
          if (text === null) return;

          const payload = typeof text === "string" ? text.trim() : text;

          // Casos OK conocidos
          if (payload === "OK") {
            if (form.action.includes("process_login.php")) {
              window.location.replace("../Vista/menu.php");
              return;
            }
            if (form.action.includes("agregar.php")) {
              window.location.replace("../Vista/login.php?ok=1");
              return;
            }
          }

          // Si devuelve HTML completo → fallback
          if (typeof payload === "string" && (payload.startsWith("<!DOCTYPE") || payload.startsWith("<html"))) {
            window.location.replace("../Vista/menu.php");
            return;
          }

          showFormError(form, (typeof payload === "string" ? payload : payload?.error) || "Ocurrió un error.");
        })
        .catch(() => showFormError(form, "Error de conexión."));
    });
  });

  // ---- Componente de error accesible
  function showFormError(form, msg) {
    let errorDiv = form.querySelector(".error-message");
    if (!errorDiv) {
      errorDiv = document.createElement("div");
      errorDiv.className = "error-message";
      errorDiv.setAttribute("role", "alert");
      errorDiv.setAttribute("aria-live", "assertive");
      const submitBtn = form.querySelector('button[type="submit"]') || form.lastElementChild;
      form.insertBefore(errorDiv, submitBtn);
    }
    errorDiv.textContent = msg || "Error.";
  }

  // ================================ Juego / Overlays ================================

  // Abre overlay de puntaje si existe
  window.mostrarOverlay = function () {
    const o = document.getElementById("puntaje-overlay");
    if (o) o.style.display = "flex";
  };

  // ---- Perfil: abrir/cerrar, cargar datos/foto
  const perfilBtn = document.getElementById("perfil-btn");
  const perfilOverlay = document.getElementById("perfil-overlay");
  if (perfilBtn && perfilOverlay) {
    perfilBtn.onclick = () => {
      perfilOverlay.style.display = "flex";
      window.cargarPerfil?.();
    };
    perfilOverlay.style.display = "none"; // inicia oculto
  }
  window.cerrarPerfilOverlay = function () {
    if (perfilOverlay) perfilOverlay.style.display = "none";
  };

  // Cargar datos perfil
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

        const perfilEmail = document.getElementById("perfil-email");
        if (perfilEmail) {
          perfilEmail.textContent = data?.email ?? "";
          if (!data?.email) perfilEmail.textContent = "";
        }

        // Actualiza foto con cache-bust
        if (data?.foto) {
          const bust = "?" + Date.now();
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
      .catch((err) => console.error("No se pudo cargar el perfil:", err));
  }
  window.cargarPerfil = cargarPerfil;
  cargarPerfil(); // refleja foto en botón al cargar

  // ---- Cambiar foto (subir al servidor)
  const fotoForm = document.getElementById("perfil-foto-form");
  const fotoInput = document.getElementById("foto-input");
  const perfilFotoBtn = document.querySelector(".perfil-foto-btn");
  const fotoErrorEl = document.getElementById("perfil-foto-error");

  function setFotoError(msg) {
    if (fotoErrorEl) fotoErrorEl.textContent = msg || "";
    else if (msg) alert(msg);
  }

  if (fotoForm && fotoInput) {
    // Validar y autoinvocar submit
    fotoInput.addEventListener("change", () => {
      setFotoError("");
      const file = fotoInput.files && fotoInput.files[0];
      if (!file) return;
      const allowed = ["image/jpeg", "image/png", "image/webp", "image/gif", "image/avif"];
      if (!allowed.includes(file.type)) return setFotoError("Formatos permitidos: PNG, JPEG, JPG, WebP, AVIF.");
      if (file.size > 3 * 1024 * 1024) return setFotoError("La imagen no debe superar 3MB.");
      if (typeof fotoForm.requestSubmit === "function") fotoForm.requestSubmit();
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

  // ---- Overlay de Opciones
  const opcionesBtn = document.getElementById("opciones-btn");
  const opcionesOverlay = document.getElementById("opciones-overlay");
  if (opcionesBtn && opcionesOverlay) {
    opcionesBtn.onclick = () => (opcionesOverlay.style.display = "flex");
    window.cerrarOpcionesOverlay = function () {
      opcionesOverlay.style.display = "none";
    };
  }

  // ---- Sonido/Música UI
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

  const musicaRange = document.getElementById("musica-range");
  const audio = document.getElementById("bg-music");
  if (audio && musicaRange) {
    audio.volume = musicaRange.value / 100;
    audio.muted = false;
    // El autoplay se maneja en el IIFE de persistencia
  }

  // ---- Toggle de Modo Oscuro (+ persistencia + notificar backend)
  document.getElementById("darkMode")?.addEventListener("change", function (e) {
    const isDarkModeNow = e.target.checked;
    document.documentElement.style.setProperty(
      "--bg-image",
      isDarkModeNow ? 'url("../img/fondoOscuro.png")' : 'url("../img/fondo.png")'
    );
    localStorage.setItem("darkMode", isDarkModeNow);
    fetch("../Controlador/save_theme.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `darkMode=${isDarkModeNow ? 1 : 0}`,
    }).catch(() => {});
  });
});

// ================================
// 🎵 AUDIO DE FONDO PERSISTENTE (IIFE)
// ================================
(function () {
  const TIME_KEY = "bg_audio_time";
  const VOLUME_KEY = "bg_audio_volume";
  const PAUSED_KEY = "bg_audio_paused";
  const SAVE_INTERVAL = 1000;

  function initBackgroundAudio() {
    const audio = document.getElementById("bg-music");
    if (!audio) return;

    // Restaurar volumen
    const storedVol = localStorage.getItem(VOLUME_KEY);
    if (storedVol !== null) {
      const v = parseFloat(storedVol);
      if (!Number.isNaN(v)) audio.volume = Math.max(0, Math.min(1, v));
    }

    // Restaurar pausa y tiempo
    const storedPaused = localStorage.getItem(PAUSED_KEY) === "1";
    const storedTime = parseFloat(localStorage.getItem(TIME_KEY)) || 0;

    function applyTime() {
      try {
        if (audio.duration && storedTime > 0 && storedTime < audio.duration) {
          audio.currentTime = storedTime;
        }
      } catch {}
      if (!storedPaused) audio.play().catch(() => {});
      else audio.pause();
    }

    if (audio.readyState >= 1) applyTime();
    else audio.addEventListener("loadedmetadata", applyTime, { once: true });

    // Guardar tiempo periódicamente
    let lastSaved = 0;
    audio.addEventListener("timeupdate", () => {
      const now = Date.now();
      if (now - lastSaved > SAVE_INTERVAL) {
        localStorage.setItem(TIME_KEY, String(Math.floor(audio.currentTime * 100) / 100));
        lastSaved = now;
      }
    });

    window.addEventListener("beforeunload", () => {
      localStorage.setItem(TIME_KEY, String(Math.floor(audio.currentTime * 100) / 100));
    });

    // Persistir volumen y pausa
    audio.addEventListener("volumechange", () => {
      localStorage.setItem(VOLUME_KEY, String(audio.volume));
    });
    audio.addEventListener("play", () => localStorage.setItem(PAUSED_KEY, "0"));
    audio.addEventListener("pause", () => localStorage.setItem(PAUSED_KEY, "1"));

    // Autoplay tras primer interacción
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

// ================================
// 🦕 DATOS DE ZONAS Y DINOS
// ================================
const DINO_TYPES = [
  { id: 1, name: "T-Rex",         img: "../img/dino1.png", points: 10 },
  { id: 2, name: "Triceratops",   img: "../img/dino2.png", points: 1 },
  { id: 3, name: "Velociraptor",  img: "../img/dino3.png", points: 1 },
  { id: 4, name: "Brachiosaurus", img: "../img/dino4.png", points: 1 },
  { id: 5, name: "Pterodactyl",   img: "../img/dino5.png", points: 1 },
  { id: 6, name: "Stegosaurus",   img: "../img/dino6.png", points: 1 },
];

// Zonas corregidas para coincidir con reglas
const ZONES = [
  { id: 1, name: "Bosque de la Semejanza", left: "4%",  top: "5%",  width: "34%", height: "23%" },
  { id: 2, name: "Prado de la Diferencia", left: "60%", top: "40%", width: "34%", height: "23%" },
  { id: 3, name: "Pradera del Amor",       left: "10%", top: "67%", width: "25%", height: "27%" },
  { id: 4, name: "Trío Frondoso",          left: "6%",  top: "37%", width: "24%", height: "23%" },
  { id: 5, name: "Rey de la Selva",        left: "69%", top: "9%",  width: "15%", height: "13%" },
  { id: 6, name: "Isla Solitaria",         left: "74%", top: "67%", width: "22%", height: "20%" },
  { id: 7, name: "El Río",                 left: "47%", top: "80%", width: "21%", height: "10%" },
];

// Límite global por especie
const MAX_PER_TYPE = 10;

// ================================
// 🍞 TOAST GLOBAL (accesible)
// ================================
function showToast(msg, timeout = 2200) {
  let t = document.getElementById("app-toast");
  if (!t) {
    t = document.createElement("div");
    t.id = "app-toast";
    t.style.position = "fixed";
    t.style.left = "50%";
    t.style.bottom = "6%";
    t.style.transform = "translateX(-50%)";
    t.style.background = "rgba(0,0,0,0.8)";
    t.style.color = "#fff";
    t.style.padding = "8px 14px";
    t.style.borderRadius = "8px";
    t.style.zIndex = 9999;
    t.style.fontSize = "14px";
    t.style.boxShadow = "0 4px 12px rgba(0,0,0,0.3)";
    t.setAttribute("role", "status");
    t.setAttribute("aria-live", "polite");
    document.body.appendChild(t);
  }
  t.textContent = msg;
  t.style.opacity = "1";
  clearTimeout(t._hideTimer);
  t._hideTimer = setTimeout(() => {
    t.style.opacity = "0";
  }, timeout);
}

// ================================
// 🧩 ESTADO DEL PARQUE Y RENDER
// ================================
let zones = {};
ZONES.forEach((zone) => (zones[zone.id] = []));
let draggedDino = null;
let draggedFromZone = null;

// Inicializa la UI del modo herramienta
function init() {
  renderDinoList();
  renderInventory();
  renderZones();
  renderScorePanel();
}

// Lista de dinos arrastrables (fuente)
function renderDinoList() {
  const container = document.getElementById("dino-list");
  if (!container) return;
  container.innerHTML = "";

  DINO_TYPES.forEach((dino) => {
    const count = getTotalByType(dino.id);
    const item = document.createElement("div");
    item.className = "dino-item";
    item.draggable = count < MAX_PER_TYPE;

    item.innerHTML = `
      <span><img src="${dino.img}" alt="${dino.name}" class="dino-img"></span>
      <span class="name">${dino.name}</span>
    `;

    if (!item.draggable) {
      item.style.opacity = "0.35";
      item.title = "Límite alcanzado (10 unidades)";
      item.addEventListener("click", () =>
        showToast("Límite alcanzado: ya hay 10 unidades de esta especie.")
      );
    }

    item.addEventListener("dragstart", (e) => {
      draggedDino = dino;
      draggedFromZone = null;
      e.dataTransfer.effectAllowed = "move";
      item.style.opacity = "0.5";
    });

    item.addEventListener("dragend", () => {
      item.style.opacity = count < MAX_PER_TYPE ? "1" : "0.35";
    });

    container.appendChild(item);
  });
}

// Inventario: totales por tipo
function renderInventory() {
  const container = document.getElementById("inventory-grid");
  if (!container) return;
  container.innerHTML = "";

  DINO_TYPES.forEach((dino) => {
    const count = getTotalByType(dino.id);
    const item = document.createElement("div");
    item.className = "inventory-item";
    item.innerHTML = `
      <div><img src="${dino.img}" alt="${dino.name}" class="dino-img"></div>
      <div class="name">${dino.name}</div>
      <div class="count">${count}</div>
    `;
    container.appendChild(item);
  });
}

// Contenedores de cada zona + eventos de drop
function renderZones() {
  const container = document.getElementById("zones-container");
  if (!container) return;
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

// Dinos dentro de una zona
function renderZoneDinos(zoneId) {
  const container = document.querySelector(`.zone-dinos[data-zone="${zoneId}"]`);
  if (!container) return;
  container.innerHTML = "";

  zones[zoneId].forEach((dino) => {
    const dinoEl = document.createElement("div");
    dinoEl.className = "zone-dino";
    dinoEl.draggable = true;

    const imgHtml = dino.img
      ? `<img src="${dino.img}" alt="${dino.name}" class="zone-dino-img">`
      : `<div class="zone-dino-placeholder"></div>`;

    dinoEl.innerHTML = `
      ${imgHtml}
      <button class="delete-btn" aria-label="Eliminar">×</button>
    `;

    // Re-arrastrar desde la zona
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

    // Eliminar dino de la zona
    const deleteBtn = dinoEl.querySelector(".delete-btn");
    deleteBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      removeDinosaur(zoneId, dino.uniqueId);
    });

    container.appendChild(dinoEl);
  });
}

// Resumen de puntaje por zona
function renderZoneSummary(zoneId) {
  const container = document.querySelector(`.zone-summary[data-zone="${zoneId}"]`);
  if (!container) return;

  const points = getZoneScore(zoneId);

  let extra = "";
  if (zoneId === 1) {
    const arr = zones[1];
    if (arr.length > 0) {
      const lockedTypeId = arr[0].id;
      const d = DINO_TYPES.find((x) => x.id === lockedTypeId);
      extra = d ? ` — Tipo fijo: ${d.name}` : "";
    }
  }
  container.textContent = `Puntos: ${points}${extra}`;
}

// Reglas de admisión por zona
function canDropInZone(zoneId, dino) {
  // Límite global por especie
  if (getTotalByType(dino.id) >= MAX_PER_TYPE) {
    showToast("No se puede colocar: ya hay 10 unidades de esta especie.");
    return false;
  }

  // 1) Bosque de la Semejanza: mismo tipo que el primero + máx 6
  if (zoneId === 1) {
    const arr = zones[1];
    if (arr.length === 0) return true;
    if (arr.length >= 6) return false;
    const lockedTypeId = arr[0].id;
    return dino.id === lockedTypeId;
  }

  // 2) Prado de la Diferencia: solo especies distintas
  if (zoneId === 2) {
    const arr = zones[2] || [];
    return !arr.some((x) => x.id === dino.id);
  }

  // 3) Pradera del Amor: sin restricción (parejas suman al puntaje)
  if (zoneId === 3) return true;

  // 4) Trío Frondoso: hasta 3 dinos
  if (zoneId === 4) {
    const currentDinos = zones[4] || [];
    return currentDinos.length < 3;
  }

  // 5) Rey de la Selva: solo 1 dino
  if (zoneId === 5) return (zones[5] || []).length === 0;

  // 6) Isla Solitaria: solo 1 dino
  if (zoneId === 6) return (zones[6] || []).length === 0;

  // 7) El Río: sin restricción
  if (zoneId === 7) return true;

  return true;
}

// Puntaje por zona
function getZoneScore(zoneId) {
  const arr = zones[zoneId] || [];
  const n = arr.length;

  // Bonus por T-Rex en el recinto (si corresponde)
  const tBonus = arr.some((d) => d.id === 1) ? 1 : 0;

  // 1) Bosque de la Semejanza
  if (zoneId === 1) {
    const table = { 1: 2, 2: 4, 3: 8, 4: 12, 5: 18, 6: 24 };
    const base = table[n] ?? (n > 6 ? 24 + (n - 6) * 4 : 0);
    return base + tBonus;
  }

  // 2) Prado de la Diferencia
  if (zoneId === 2) {
    const distinct = new Set(arr.map((d) => d.id)).size;
    return distinct + tBonus;
  }

  // 3) Pradera del Amor (5 pts por pareja)
  if (zoneId === 3) {
    let pairs = 0;
    for (const d of DINO_TYPES) {
      const c = arr.filter((x) => x.id === d.id).length;
      pairs += Math.floor(c / 2);
    }
    const base = pairs * 5;
    return base + tBonus;
  }

  // 4) Trío Frondoso (exactamente 3 → 7 pts)
  if (zoneId === 4) {
    const base = n === 3 ? 7 : 0;
    return base + tBonus;
  }

  // 5) Rey de la Selva (1 dino; si es el recinto más poblado del parque → 7, sino 1)
  if (zoneId === 5) {
    if (n === 0) return 0;
    const others = Object.entries(zones)
      .filter(([k]) => Number(k) !== 5)
      .map(([, a]) => a.length);
    const strictlyMost = n > Math.max(0, ...others);
    const base = strictlyMost ? 7 : 1;
    return base + tBonus;
  }

  // 6) Isla Solitaria (1 dino; si es el único de su especie en todo el parque → 7)
  if (zoneId === 6) {
    if (n === 0) return 0;
    const onlyDino = arr[0];
    const totalSame = getTotalByType(onlyDino.id);
    const base = totalSame === 1 ? 7 : 0;
    return base + tBonus;
  }

  // 7) El Río (1 punto por dino)
  if (zoneId === 7) {
    const base = n;
    return base + tBonus;
  }

  return 0;
}

// Maneja el drop (mover/crear ficha) y refresca UI
function handleDrop(zoneId) {
  if (!draggedDino) return;

  const canDrop = canDropInZone(zoneId, draggedDino);
  if (!canDrop) {
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

  // Reubicar o crear nuevo con uniqueId
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

// Eliminar dino por uniqueId
function removeDinosaur(zoneId, uniqueId) {
  zones[zoneId] = zones[zoneId].filter((d) => d.uniqueId !== uniqueId);
  renderInventory();
  renderZones();
  renderScorePanel();
}

// Helpers de conteo
function getDinoCount(zoneId, dinoId) {
  return zones[zoneId].filter((d) => d.id === dinoId).length;
}
function getTotalByType(dinoId) {
  return Object.values(zones).flat().filter((d) => d.id === dinoId).length;
}

// Arranque del modo herramienta
init();

// ================================
// 🧭 OVERLAY INFORMATIVO: carrusel
// ================================
(function () {
  const overlay = document.getElementById("tool-overlay");
  const openOnLoad = true; // abrir automáticamente al cargar (opcional)
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
    overlay.animate([{ opacity: 0 }, { opacity: 1 }], {
      duration: 220,
      easing: "ease-out",
    });
    setTimeout(() => {
      (
        overlay.querySelector(".tool-close, .tool-nav, .tool-indicator") || overlay
      ).focus?.();
    }, 200);
  }

  function closeOverlay() {
    overlay.classList.add("hidden");
    overlay.style.display = "none";
    index = 0; // reinicia al primer slide (opcional)
    update();
  }

  prevBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    if (index > 0) {
      index--;
      update();
      track?.animate(
        [
          { transform: `translateX(-${(index + 1) * 100}%)`, offset: 0 },
          { transform: `translateX(-${index * 100}%)`, offset: 1 },
        ],
        { duration: 350, easing: "cubic-bezier(0.4, 0, 0.2, 1)" }
      );
    }
  });

  nextBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    if (index < slides.length - 1) {
      index++;
      update();
      track?.animate(
        [
          { transform: `translateX(-${(index - 1) * 100}%)`, offset: 0 },
          { transform: `translateX(-${index * 100}%)`, offset: 1 },
        ],
        { duration: 350, easing: "cubic-bezier(0.4, 0, 0.2, 1)" }
      );
    }
  });

  closeBtn?.addEventListener("click", (e) => {
    e.stopPropagation();
    closeOverlay();
  });

  infoBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    openOverlay();
  });

  // Cierra al hacer click fuera del modal
  overlay.addEventListener("click", (e) => {
    const modal = overlay.querySelector(".tool-modal");
    if (e.target === overlay) closeOverlay();
    if (modal && !modal.contains(e.target)) closeOverlay();
  });

  // Navegación por teclado
  document.addEventListener("keydown", (e) => {
    if (overlay.classList.contains("hidden")) return;
    if (e.key === "Escape") {
      e.preventDefault();
      closeOverlay();
      return;
    }
    if (e.key === "ArrowLeft" && index > 0) {
      index--;
      update();
    }
    if (e.key === "ArrowRight" && index < slides.length - 1) {
      index++;
      update();
    }
  });

  function initToolOverlay() {
    if (!track) return;
    track.style.width = `${slides.length * 100}%`;
    slides.forEach((s) => (s.style.width = `${100 / slides.length}%`));
    update();
    if (openOnLoad) openOverlay();
  }

  window.openToolOverlay = openOverlay;
  window.closeToolOverlay = closeOverlay;

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initToolOverlay);
  } else initToolOverlay();
})();

// ================================
// 📜 PANEL DE PUNTAJES (lista por zona + total)
// ================================
function renderScorePanel() {
  const ul = document.getElementById("zone-scores");
  if (!ul) return;
  ul.innerHTML = "";
  let total = 0;
  ZONES.forEach((zone) => {
    const puntos = getZoneScore(zone.id);
    total += puntos;
    const li = document.createElement("li");
    li.style.fontFamily = "agbalumo, sans-serif";
    li.style.paddingBottom = "8px";
    li.style.textAlign = "left";
    li.style.margin = "4px 0";
    li.style.borderRadius = "6px";
    li.style.transition = "background-color 0.2s";
    li.onmouseover = () => li.style.backgroundColor = "rgba(255, 255, 255, 0.15)";
    li.onmouseout = () => li.style.backgroundColor = "rgba(255, 255, 255, 0.1)";
    li.textContent = `${zone.name}: ${puntos} puntos`;
    ul.appendChild(li);
  });
  const totalLi = document.createElement("li");
  totalLi.style.fontWeight = "bold";
  totalLi.style.marginTop = "8px";
  totalLi.textContent = `Total: ${total} puntos`;
  ul.appendChild(totalLi);
}

// ================================
// ⏮️ BOTÓN VOLVER CON AUDIO
// ================================
function handleBackButton() {
  const volverAudio = document.getElementById("volver-audio");
  if (volverAudio) {
    volverAudio.currentTime = 0;
    volverAudio
      .play()
      .then(() => {
        setTimeout(() => {
          window.history.back();
        }, 200);
      })
      .catch(() => {
        window.history.back();
      });
  } else {
    window.history.back();
  }
}

// ================================
// 👤/🔎 BLOQUE DE COMPATIBILIDAD (re-registro de handlers por si se recargan fragmentos)
// ================================
document.addEventListener("DOMContentLoaded", () => {
  // Perfil
  const perfilBtn = document.getElementById("perfil-btn");
  const perfilOverlay = document.getElementById("perfil-overlay");
  if (perfilBtn && perfilOverlay) {
    perfilBtn.onclick = () => {
      perfilOverlay.style.display = "flex";
      window.cargarPerfil?.();
    };
  }
  window.cerrarPerfilOverlay = function () {
    if (perfilOverlay) perfilOverlay.style.display = "none";
  };

  // Overlay de código (buscar partida)
  const buscarBtn = document.getElementById("buscar-codigo-btn");
  const codigoOverlay = document.getElementById("codigo-overlay");
  const cerrarBtn = document.querySelector(".close-codigo");
  const codigoForm = document.getElementById("codigo-form");
  const codigoInput = document.getElementById("codigo-input");
  const toggleBtn = document.getElementById("toggle-password");

  function abrirCodigoOverlay() {
    if (codigoOverlay) {
      codigoOverlay.style.display = "flex";
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
      toggleBtn.setAttribute("aria-pressed", String(codigoInput.type === "text"));
    });
  }
  if (codigoForm && codigoInput) {
    codigoForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const code = codigoInput.value.trim();
      if (!code) return;
      window.location.href = `lobby.php?code=${encodeURIComponent(code)}`;
    });
  }
});

// ================================
// 🗂️ CAMBIO DE TABS (utilidad simple)
// ================================
function switchTab(tab, ev) {
  document.querySelectorAll(".tab").forEach((t) => t.classList.remove("active"));
  const clicked = ev?.currentTarget || ev?.target;
  if (clicked) clicked.classList.add("active");

  document.querySelectorAll(".tab-content").forEach((c) => c.classList.remove("active"));
  document.getElementById(tab).classList.add("active");
}
