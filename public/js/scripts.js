// Inicializar iconos de Lucide
lucide.createIcons();

// Reloj en vivo en el Header
function updateClock() {
    const clockEl = document.getElementById("live-clock");
    if (clockEl) {
        const now = new Date();
        clockEl.textContent = now.toLocaleTimeString("es-ES", {
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
        });
    }
}
setInterval(updateClock, 1000);
updateClock();

// Control de menú de navegación responsivo
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebar-overlay");

    sidebar.classList.toggle("sidebar--open");
    overlay.classList.toggle("sidebar-overlay--visible");
}

// --- SISTEMA DE DIÁLOGO MODAL PERSONALIZADO ---
function mostrarNotificacion(titulo, mensaje, tipoColor, icono) {
    const modalOverlay = document.getElementById("modal-notificacion");
    const headerIcono = document.getElementById("notificacion-icono");
    const tituloEl = document.getElementById("notificacion-titulo");
    const mensajeEl = document.getElementById("notificacion-mensaje");

    tituloEl.textContent = titulo;
    mensajeEl.textContent = mensaje;

    headerIcono.className = "modal__icon-box";
    if (tipoColor === "accent") {
        headerIcono.classList.add("modal__icon-box--accent");
    } else if (tipoColor === "terracotta") {
        headerIcono.classList.add("modal__icon-box--terracotta");
    } else {
        headerIcono.classList.add("modal__icon-box--primary");
    }

    headerIcono.innerHTML = `<i data-lucide="${icono}" class="modal__icon"></i>`;
    lucide.createIcons();

    modalOverlay.classList.add("modal-overlay--visible");
}

function cerrarNotificacion() {
    document
        .getElementById("modal-notificacion")
        .classList.remove("modal-overlay--visible");
}

function openVentaRapida() {
    // ID: 999, Nombre: "Café Cappuccino", Precio: 3.5, Stock Máximo: 10
    agregarProducto(999, "Café Cappuccino", 3.5, 10);

    mostrarNotificacion(
        "¡Producto Añadido!",
        "Hemos agregado automáticamente un Café Cappuccino para que inicies la orden rápido.",
        "primary",
        "coffee",
    );
}
