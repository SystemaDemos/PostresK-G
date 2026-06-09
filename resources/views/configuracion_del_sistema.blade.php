@extends('layouts.app')

@section('title', 'Configuración del Sistema - PostresK&G')

@section('content')
    <div class="app__content">

        <header class="page-header">
            <h2 class="page-header__title">Configuración del Sistema</h2>
            <p class="page-header__subtitle">Administra respaldos, optimiza bases de datos y gestiona los parámetros
                generales del terminal.</p>
        </header>

        <div class="config-grid">

            <!-- Tarjeta 1: Copias de Seguridad (Backup y Restauración) -->
            <div class="pos-card config-card">
                <div class="config-card__body">
                    <div class="metric-card__icon-box metric-card__icon-box--primary">
                        <i data-lucide="database-backup" class="metric-card__icon"></i>
                    </div>
                    <div class="config-card__txt">
                        <h3 class="config-card__title">Copias de Seguridad (Backup)</h3>
                        <p class="config-card__description">
                            Resguarda tu información descargando una copia completa del sistema o restaura un archivo
                            `.sqlite`/`.sql` guardado previamente para recuperar tus datos.
                        </p>
                    </div>
                </div>

                <div class="config-card__footer config-card__footer--split">
                    <!-- FORMULARIO 1: Restaurar Respaldo (Izquierda) -->
                    <form action="{{ route('configuracion.restaurar') }}" method="POST" enctype="multipart/form-data"
                        id="form-restaurar">
                        @csrf
                        <!-- Input de archivo oculto que se dispara por JS -->
                        <input type="file" name="archivo_backup" id="input-backup-file" style="display: none;"
                            onchange="confirmarRestauracion(this)">

                        <button type="button" class="config-card__btn config-card__btn--outline"
                            onclick="seleccionarArchivoBackup()">
                            <i data-lucide="upload" style="width: 1rem; height: 1rem;"></i>
                            <span>Restaurar Respaldo</span>
                        </button>
                    </form>

                    <!-- FORMULARIO 2: Crear Respaldo (Derecha) -->
                    <form action="{{ route('configuracion.respaldar') }}" method="POST" id="form-respaldo">
                        @csrf
                        <button type="button" class="config-card__btn config-card__btn--primary"
                            onclick="ejecutarRespaldo()">
                            <i data-lucide="download" style="width: 1rem; height: 1rem;"></i>
                            <span>Crear Respaldo</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="pos-card config-card">
                <div class="config-card__body">
                    <div class="metric-card__icon-box metric-card__icon-box--secondary">
                        <i data-lucide="zap" class="metric-card__icon"></i>
                    </div>
                    <div class="config-card__txt">
                        <h3 class="config-card__title">Optimización de Tablas</h3>
                        <p class="config-card__description">
                            Optimiza el rendimiento consolidando la caché del sistema y ejecuta un reindexado físico para
                            compactar la base de datos.
                        </p>
                    </div>
                </div>
                <div class="config-card__footer">
                    <form action="{{ route('configuracion.optimizar') }}" method="POST" id="form-optimizar">
                        @csrf
                        <button type="button" class="config-card__btn config-card__btn--secondary"
                            onclick="ejecutarOptimizacion()">
                            <i data-lucide="sparkles" style="width: 1rem; height: 1rem;"></i>
                            <span>Optimizar Terminal</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="pos-card config-card">
                <div class="config-card__body">
                    <div class="metric-card__icon-box metric-card__icon-box--terracotta">
                        <i data-lucide="refresh-cw" class="metric-card__icon"></i>
                    </div>
                    <div class="config-card__txt">
                        <h3 class="config-card__title">Vaciar Inventario a Cero</h3>
                        <p class="config-card__description">
                            Establece las cantidades de todos los productos del sistema a **0 u.** de forma masiva. Útil
                            para auditorías anuales complejas.
                        </p>
                    </div>
                </div>
                <div class="config-card__footer">
                    <form action="{{ route('configuracion.reset-stock') }}" method="POST" id="form-reset-stock">
                        @csrf
                        @method('PUT')
                        <button type="button" class="config-card__btn config-card__btn--danger"
                            onclick="confirmarResetInventario()">
                            <i data-lucide="alert-triangle" style="width: 1rem; height: 1rem;"></i>
                            <span>Reiniciar Stock</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <style>
        .page-header {
            margin-bottom: 2rem;
        }

        .page-header__title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--color-slate-800, #1e293b);
            margin-bottom: 0.25rem;
        }

        .page-header__subtitle {
            font-size: 0.9rem;
            color: var(--color-slate-500, #64748b);
        }

        /* Grilla Rectangular */
        .config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 1.5rem;
            width: 100%;
        }

        /* Tarjeta de configuración */
        .config-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem;
            background-color: var(--color-white, #ffffff);
            border-radius: var(--radius-lg, 0.75rem);
            border: 1px solid var(--color-slate-100, #e2e8f0);
            transition: var(--transition-smooth, all 0.2s ease);
        }

        .config-card:hover {
            box-shadow: var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.05));
        }

        .config-card__body {
            display: flex;
            gap: 1.25rem;
            align-items: flex-start;
        }

        .config-card__txt {
            flex: 1;
        }

        .config-card__title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--color-slate-800, #1e293b);
            margin: 0 0 0.5rem 0;
        }

        .config-card__description {
            font-size: 0.875rem;
            color: var(--color-slate-500, #64748b);
            line-height: 1.5;
            margin: 0;
        }

        .config-card__footer {
            margin-top: 1.5rem;
            display: flex;
            justify-content: flex-end;
            border-top: 1px solid var(--color-slate-50, #f8fafc);
            padding-top: 1rem;
        }

        /* Botonera unificada con tus variables */
        .config-card__btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: var(--radius-sm, 0.375rem);
            cursor: pointer;
            transition: var(--transition-smooth, all 0.2s ease);
            border: 1px solid transparent;
            font-family: inherit;
        }

        /* Botón Primario (Morado KoraPOS) */
        .config-card__btn--primary {
            background-color: var(--color-purple-bg, #f3e8ff);
            color: var(--color-primary, #9472b8);
        }

        .config-card__btn--primary:hover {
            background-color: var(--color-primary, #9472b8);
            color: white;
        }

        /* Permite empujar los formularios a los extremos opuestos */
        .config-card__footer--split {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            width: 100%;
        }

        /* Botón Primario (Morado KoraPOS) */
        .config-card__btn--primary {
            background-color: var(--color-purple-bg, #f3e8ff);
            color: var(--color-primary, #9472b8);
        }

        .config-card__btn--primary:hover {
            background-color: var(--color-primary, #9472b8);
            color: white;
        }

        /* Botón de Restaurar (Estilo Outline Sutil) */
        .config-card__btn--outline {
            background-color: var(--color-purple-bg, #f3e8ff);
            color: var(--color-primary, #9472b8);
        }

        .config-card__btn--outline:hover {
            background-color: var(--color-primary, #9472b8);
            color: white;
        }

        /* Botón Secundario (Estilo Info/Accent) */
        .config-card__btn--secondary {
            background-color: rgba(188, 70, 133, 0.08);
            color: #bc4685;
        }

        .config-card__btn--secondary:hover {
            background-color: #bc4685;
            color: white;
        }

        /* Botón Danger (Terracota Advertencias) */
        .config-card__btn--danger {
            background-color: #fee2e2;
            color: var(--color-terracotta, #e06666);
            border-color: rgba(184, 70, 84, 0.1);
        }

        .config-card__btn--danger:hover {
            background-color: var(--color-terracotta, #e06666);
            color: white;
        }

        /* Ajustes Mobile */

        @media (max-width: 480px) {
            .config-card__footer--split {
                flex-direction: column-reverse;
            }

            .config-card__footer--split form {
                width: 100%;
            }
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // 1. Manejo del Respaldo
        function ejecutarRespaldo() {
            if (typeof mostrarNotificacion === 'function') {
                mostrarNotificacion(
                    "Generando Copia...",
                    "La base de datos se empaquetará de manera segura. Por favor, descarga y resguarda el archivo resultante.",
                    "primary",
                    "database"
                );
            }

            // Cambiar los botones a Confirmar / Cancelar en el contenedor global
            const accionesBox = document.getElementById('notificacion-acciones');
            if (accionesBox) {
                accionesBox.innerHTML = `
                    <div style="display: flex; gap: 1rem; width: 100%; justify-content: center; margin-top: 1rem;">
                        <button type="button" onclick="cerrarNotificacion()" class="modal__btn" style="background-color: #000; color: #475569; border: 1px solid #cbd5e1; margin: 0;">
                            Cancelar
                        </button>
                        <button type="button" onclick="document.getElementById('form-respaldo').submit()" class="modal__btn" style="background-color: var(--color-primary, #9472b8); color: white; margin: 0;">
                            Descargar DB
                        </button>
                    </div>
                `;
            }
        }

        // Abre el selector de archivos del sistema operativo al pulsar "Restaurar"
        function seleccionarArchivoBackup() {
            document.getElementById('input-backup-file').click();
        }

        // Si el usuario selecciona un archivo, interceptamos el flujo con tu modal global
        function confirmarRestauracion(input) {
            if (!input.files || input.files.length === 0) return;

            const nombreArchivo = input.files[0].name;

            if (typeof mostrarNotificacion === 'function') {
                mostrarNotificacion(
                    "¿Restaurar Base de Datos?",
                    `¡Atención! Estás a punto de reemplazar toda la base de datos actual con el archivo "${nombreArchivo}". Las ventas y productos actuales se perderán permanentemente.`,
                    "terracotta", // Color terracota de advertencia crítica
                    "alert-circle"
                );
            }

            const accionesBox = document.getElementById('notificacion-acciones');
            if (accionesBox) {
                accionesBox.innerHTML = `
                    <div style="display: flex; gap: 1rem; width: 100%; justify-content: center; margin-top: 1rem;">
                        <button type="button" onclick="cancelarRestauracion()" class="modal__btn" style="background-color: #000; color: #475569; border: 1px solid #cbd5e1; margin: 0;">
                            Cancelar
                        </button>
                        <button type="button" onclick="document.getElementById('form-restaurar').submit()" class="modal__btn" style="background-color: var(--color-terracotta, #e06666); color: white; margin: 0;">
                            Sí, Restaurar Todo
                        </button>
                    </div>
                `;
            }
        }

        // Limpia el input por si el usuario se arrepiente y decide subir otro archivo después
        function cancelarRestauracion() {
            document.getElementById('input-backup-file').value = "";
            cerrarNotificacion();
        }

        // 2. Manejo de la Optimización
        function ejecutarOptimizacion() {
            if (typeof mostrarNotificacion === 'function') {
                mostrarNotificacion(
                    "Optimizar Terminal",
                    "Esta operación reorganizará los índices físicos y purgará archivos basura para acelerar las consultas en caja.",
                    "accent",
                    "zap"
                );
            }

            const accionesBox = document.getElementById('notificacion-acciones');
            if (accionesBox) {
                accionesBox.innerHTML = `
                    <div style="display: flex; gap: 1rem; width: 100%; justify-content: center; margin-top: 1rem;">
                        <button type="button" onclick="cerrarNotificacion()" class="modal__btn" style="background-color: #000; color: #475569; border: 1px solid #cbd5e1; margin: 0;">
                            Cancelar
                        </button>
                        <button type="button" onclick="document.getElementById('form-optimizar').submit()" class="modal__btn" style="background-color: #bc4685; color: white; margin: 0;">
                            Optimizar Ahora
                        </button>
                    </div>
                `;
            }
        }

        // 3. Manejo de Operación Peligrosa (Reset Inventario)
        function confirmarResetInventario() {
            if (typeof mostrarNotificacion === 'function') {
                mostrarNotificacion(
                    "¿Establecer Stock en Cero?",
                    "¡Cuidado! Esta acción cambiará las cantidades de TODOS tus productos a 0 u. No eliminará los nombres ni categorías, pero el inventario quedará vacío.",
                    "terracotta",
                    "alert-triangle"
                );
            }

            const accionesBox = document.getElementById('notificacion-acciones');
            if (accionesBox) {
                accionesBox.innerHTML = `
                    <div style="display: flex; gap: 1rem; width: 100%; justify-content: center; margin-top: 1rem;">
                        <button type="button" onclick="cerrarNotificacion()" class="modal__btn" style="background-color: #000; color: #475569; border: 1px solid #cbd5e1; margin: 0;">
                            Cancelar
                        </button>
                        <button type="button" onclick="document.getElementById('form-reset-stock').submit()" class="modal__btn" style="background-color: var(--color-terracotta, #e06666); color: white; margin: 0;">
                            Sí, Vaciar Stock
                        </button>
                    </div>
                `;
            }
        }
    </script>
@endsection
