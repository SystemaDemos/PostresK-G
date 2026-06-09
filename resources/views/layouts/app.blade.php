<!DOCTYPE html>
<html lang="es" class="app-html">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PostresK&G')</title>
    <!-- Lucide Icons para la iconografía limpia y minimalista -->
    <script src="{{ asset('js/lucide.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>

<body>

    <!-- Contenedor Principal (Sidebar + Contenido) -->
    <div class="app">

        <!-- BARRA DE NAVEGACIÓN IZQUIERDA (SIDEBAR) -->
        <aside id="sidebar" class="sidebar">

            <div class="sidebar__top-group">
                <!-- Header del Sidebar -->
                <div class="sidebar__header">
                    <div class="sidebar__brand-wrapper">
                        <div class="sidebar__logo-box">
                            <i data-lucide="layout-dashboard" class="sidebar__logo-icon"></i>
                        </div>
                        <div>
                            <h1 class="sidebar__title">Postres<span class="sidebar__title-highlight">K&G</span></h1>
                            <span class="sidebar__subtitle">Bodega del Materno</span>
                        </div>
                    </div>
                    <!-- Botón Cerrar Sidebar en Móvil -->
                    <button onclick="toggleSidebar()" class="sidebar__close-btn">
                        <i data-lucide="x"></i>
                    </button>
                </div>

                <!-- Menú de Navegación -->
                <nav class="sidebar__nav">
                    <a href="/"
                        class="sidebar__nav-link {{ request()->is('/') ? 'sidebar__nav-link--active' : '' }}">
                        <i data-lucide="layout-dashboard" class="sidebar__nav-icon"></i>
                        <span>Panel General</span>
                    </a>
                    <a href="/punto-de-venta"
                        class="sidebar__nav-link {{ request()->is('punto-de-venta*') ? 'sidebar__nav-link--active' : '' }}">
                        <i data-lucide="shopping-cart" class="sidebar__nav-icon"></i>
                        <span>Punto de Venta</span>
                    </a>
                    <a href="/productos"
                        class="sidebar__nav-link {{ request()->is('productos*') ? 'sidebar__nav-link--active' : '' }}">
                        <i data-lucide="package" class="sidebar__nav-icon"></i>
                        <span>Productos</span>
                    </a>
                    <a href="/agregar-producto"
                        class="sidebar__nav-link {{ request()->is('agregar-producto*') ? 'sidebar__nav-link--active' : '' }}">
                        <i data-lucide="plus" class="sidebar__nav-icon"></i>
                        <span>Agregar Producto</span>
                    </a>
                    <a href="/estadisticas"
                        class="sidebar__nav-link {{ request()->is('estadisticas*') ? 'sidebar__nav-link--active' : '' }}">
                        <i data-lucide="bar-chart-3" class="sidebar__nav-icon"></i>
                        <span>Estadísticas</span>
                    </a>
                    <a href="/configuracion"
                        class="sidebar__nav-link {{ request()->is('configuracion*') ? 'sidebar__nav-link--active' : '' }}">
                        <i data-lucide="settings" class="sidebar__nav-icon"></i>
                        <span>Configuración</span>
                    </a>
                </nav>
            </div>

        </aside>

        <!-- Overlay para móviles cuando el sidebar está abierto -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="sidebar-overlay"></div>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="app__main">

            <!-- HEADER SUPERIOR -->

            <!-- CUERPO PRINCIPAL DEL DASHBOARD -->
            @yield('content')

            <!-- MODAL NOTIFICACIÓN PERSONALIZADA -->
            <div id="modal-notificacion" class="modal-overlay">
                <div class="modal">
                    <div id="notificacion-icono" class="modal__icon-box">
                    </div>
                    <h4 id="notificacion-titulo" class="modal__title"></h4>
                    <p id="notificacion-mensaje" class="modal__message"></p>

                    <div id="notificacion-acciones" class="modal__actions">
                        <button onclick="cerrarNotificacion()" class="modal__btn">Entendido</button>
                    </div>
                </div>
            </div>

            <!-- PIE DE PÁGINA (FOOTER) -->
            <footer class="footer">
                <div class="footer__container">
                    <div class="footer__copyright">
                        &copy; 2026 <strong class="footer__copyright-highlight">PostresK&G</strong>. Sistema POS punto de venta e inventario.
                    </div>
                </div>
            </footer>

        </main>

    </div>

    <!-- SCRIPT DE LÓGICA E INTERACTIVIDAD -->
    <script src="{{ asset('js/scripts.js') }}"></script>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Permitir personalizar el título de la notificación de éxito, con un valor predeterminado si no se proporciona
                let tituloExito = "{{ session('title') ?? '¡Operación Exitosa!' }}";
                mostrarNotificacion(tituloExito, "{{ session('success') }}", "accent", "check");
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let tituloError = "{{ session('title') ?? 'Error del Sistema' }}";
                mostrarNotificacion(tituloError, "{{ session('error') }}", "terracotta", "alert-circle");
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let mensajeError = "{{ $errors->first() }}";

                // Traductor inteligente universal para el Kiosco
                if (mensajeError.includes('nombre')) mensajeError = "El nombre del producto es obligatorio.";
                if (mensajeError.includes('cantidad')) mensajeError =
                    "La cantidad debe ser un número entero válido (0 o mayor).";
                if (mensajeError.includes('precio')) mensajeError =
                    "El valor de venta debe ser un precio numérico válido.";

                mostrarNotificacion("Datos Incorrectos", mensajeError, "terracotta", "alert-circle");
            });
        </script>
    @endif
</body>

</html>
