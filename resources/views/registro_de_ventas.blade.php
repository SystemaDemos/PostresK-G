@extends('layouts.app')

@section('title',
    'Ventas individuales - PostresK&G' .
    ($vista === 'detalles'
    ? 'Desglose Detallado'
    : 'Historial
    Individual'))

@section('content')
    <div class="app__content">

        <div class="page-header">
            <div class="page-header__title-box">
                <a href="{{ url('/punto-de-venta') }}" class="page-header__back-btn">
                    <i data-lucide="arrow-left" style="width: 1.25rem; height: 1.25rem;"></i>
                    <span>Volver al POS</span>
                </a>
                <h2 class="page-header__title">
                    {{ $vista === 'detalles' ? 'Desglose Detallado de Ventas' : 'Historial de Ventas Individuales' }}
                </h2>
                <p class="page-header__subtitle">Consulta y auditoría de operaciones registradas en el sistema</p>
            </div>

            <div class="page-header__actions">
                <div class="header-actions">
                    <button type="button" class="btn-action btn-action--print" onclick="generarReportePDF()">
                        <i data-lucide="printer" style="width: 1rem; height: 1rem;"></i>
                        <span>Imprimir</span>
                    </button>

                    <form id="form-vaciar-historial" action="{{ route('ventas.limpiar') }}" method="POST"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')

                        <button type="button" class="btn-action btn-action--clear" onclick="abrirModalVaciarHistorial()">
                            <i data-lucide="trash-2" style="width: 1rem; height: 1rem;"></i>
                            <span>Limpiar Tabla</span>
                        </button>
                    </form>
                </div>

                <div class="view-switcher">
                    <a href="{{ route('ventas.registro', ['vista' => 'individuales']) }}"
                        class="view-switcher__tab {{ $vista === 'individuales' ? 'view-switcher__tab--active' : '' }}">
                        <i data-lucide="receipt" style="width: 1rem; height: 1rem;"></i>
                        <span>Ventas Individuales</span>
                    </a>
                    <a href="{{ route('ventas.registro', ['vista' => 'detalles']) }}"
                        class="view-switcher__tab {{ $vista === 'detalles' ? 'view-switcher__tab--active' : '' }}">
                        <i data-lucide="bar-chart-3" style="width: 1rem; height: 1rem;"></i>
                        <span>Detalle por Productos</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="pos-card registry-card">
            <div class="registry-table-wrapper">

                @if ($ventas->isEmpty())
                    <div class="registry-empty">
                        <i data-lucide="folder-open" class="registry-empty__icon"></i>
                        <p class="registry-empty__text">No se han encontrado registros de ventas en el sistema.</p>
                    </div>
                @else
                    <div id="encabezado-pdf"
                        style="display: none; font-family: system-ui, sans-serif; margin-bottom: 20px;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #bc4685; padding-bottom: 10px;">
                            <div>
                                <h1 style="margin: 0; font-size: 24px; color: #1e293b;">Postres<span
                                        style="color: #bc4685;">K&G</span></h1>
                                <p style="margin: 4px 0 0 0; font-size: 12px; color: #64748b;">Reportes de Operaciones del
                                    Sistema</p>
                            </div>
                            <div style="text-align: right;">
                                <h3 style="margin: 0; font-size: 14px; color: #1e293b;">
                                    {{ $vista === 'detalles' ? 'AUDITORÍA: DESGLOSE DE PRODUCTOS' : 'REPORTE: BALANCE DE VENTAS' }}
                                </h3>
                                <p style="margin: 4px 0 0 0; font-size: 11px; color: #475569;"><strong>Generado:</strong>
                                    {{ now()->format('d/m/Y h:i A') }}</p>
                            </div>
                        </div>
                        <p
                            style="font-size: 12px; color: #334155; background: #f8fafc; border-left: 4px solid #bc4685; padding: 10px; margin-top: 15px; border-radius: 0 4px 4px 0; line-height: 1.4;">
                            @if ($vista === 'detalles')
                                Este documento detalla los ítems y artículos vendidos de forma desglosada, especificando
                                cantidades y subtotales en divisas junto a su equivalencia en bolívares según la tasa
                                registrada de cada transacción.
                            @else
                                Este reporte consolida el historial de facturas emitidas de manera individual, reflejando
                                los métodos de pago utilizados y los totales generales percibidos en taquilla.
                            @endif
                        </p>
                    </div>

                    <table class="registry-table">
                        <thead>
                            @if ($vista === 'individuales')
                                <tr>
                                    <th>Ref. Factura</th>
                                    <th>Cliente</th>
                                    <th>Método de Pago</th>
                                    <th>Tasa Ref.</th>
                                    <th>Total USD</th>
                                    <th>Total Bs.</th>
                                    <th>Fecha y Hora</th>
                                </tr>
                            @else
                                <tr>
                                    <th>Ref. Factura</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit. ($)</th>
                                    <th>Subtotal ($)</th>
                                    <th>Subtotal Bs.</th>
                                    <th>Fecha y Hora</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @if ($vista === 'individuales')
                                @foreach ($ventas as $venta)
                                    <tr class="registry-table__row">
                                        <td class="registry-table__td registry-table__td--bold">
                                            #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
                                        <td class="registry-table__td">{{ $venta->cliente_nombre }}</td>
                                        <td class="registry-table__td">
                                            <span class="badge badge--info">{{ $venta->metodo_pago }}</span>
                                        </td>
                                        <td class="registry-table__td text-muted">
                                            {{ number_format($venta->tasa_cambio, 2, ',', '.') }} Bs.</td>
                                        <td class="registry-table__td registry-table__td--usd">
                                            ${{ number_format($venta->total_usd, 2, ',', '.') }}</td>
                                        <td class="registry-table__td registry-table__td--bs">
                                            {{ number_format($venta->total_bs, 2, ',', '.') }} Bs.</td>
                                        <td class="registry-table__td text-muted">
                                            {{ $venta->created_at->format('d/m/Y h:i A') }}</td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach ($ventas as $venta)
                                    @foreach ($venta->detalles as $detalle)
                                        <tr class="registry-table__row">
                                            <td class="registry-table__td text-muted">
                                                #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
                                            <td class="registry-table__td registry-table__td--bold">
                                                {{ $detalle->producto->nombre ?? 'Producto Removido' }}</td>
                                            <td class="registry-table__td">{{ $detalle->cantidad }} u.</td>
                                            <td class="registry-table__td">
                                                ${{ number_format($detalle->precio_unitario_usd, 2, ',', '.') }}</td>
                                            <td class="registry-table__td registry-table__td--usd">
                                                ${{ number_format($detalle->precio_unitario_usd * $detalle->cantidad, 2, ',', '.') }}
                                            </td>
                                            <td class="registry-table__td registry-table__td--bs">
                                                {{ number_format($detalle->precio_unitario_usd * $detalle->cantidad * $venta->tasa_cambio, 2, ',', '.') }}
                                                Bs.
                                            </td>
                                            <td class="registry-table__td text-muted">
                                                {{ $venta->created_at->format('d/m/Y h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                @endif

            </div>

            <div class="data-table__footer">
                <p class="data-table__footer-text">
                    Mostrando <span class="data-table__footer-highlight">{{ $ventas->firstItem() ?? 0 }}</span>
                    al <span class="data-table__footer-highlight">{{ $ventas->lastItem() ?? 0 }}</span>
                    de <span id="tabla-conteo-filas" class="data-table__footer-highlight">{{ $ventas->total() }}</span>
                    {{ $vista === 'detalles' ? 'productos desglosados' : 'facturas individuales' }}
                </p>

                {{-- Comprobamos si hay más de una página para mostrar la navegación --}}
                @if ($ventas->hasPages())
                    <nav class="pagination">
                        {{-- Botón Anterior --}}
                        @if ($ventas->onFirstPage())
                            <span class="pagination__btn pagination__btn--disabled">
                                <i data-lucide="chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $ventas->previousPageUrl() }}" class="pagination__btn">
                                <i data-lucide="chevron-left"></i>
                            </a>
                        @endif

                        {{-- Números de Páginas con Ventana Inteligente (...) --}}
                        <div class="pagination__pages">
                            @foreach ($ventas->onEachSide(1)->linkCollection() as $link)
                                @if (!str_contains($link['label'], 'Previous') && !str_contains($link['label'], 'Next'))
                                    @if (is_null($link['url']))
                                        <span class="pagination__link"
                                            style="cursor: default; color: var(--color-slate-400); user-select: none;">...</span>
                                    @elseif ($link['active'])
                                        <span class="pagination__link pagination__link--active">{{ $link['label'] }}</span>
                                    @else
                                        <a href="{{ $link['url'] }}" class="pagination__link">{{ $link['label'] }}</a>
                                    @endif
                                @endif
                            @endforeach
                        </div>

                        {{-- Botón Siguiente --}}
                        @if ($ventas->hasMorePages())
                            <a href="{{ $ventas->nextPageUrl() }}" class="pagination__btn">
                                <i data-lucide="chevron-right"></i>
                            </a>
                        @else
                            <span class="pagination__btn pagination__btn--disabled">
                                <i data-lucide="chevron-right"></i>
                            </span>
                        @endif
                    </nav>
                @endif
            </div>
        </div>

    </div>

    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 2rem;
        }

        .page-header__back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            transition: color 0.2s;
        }

        .page-header__back-btn:hover {
            color: #bc4685;
        }

        .page-header__title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .page-header__subtitle {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0.25rem 0 0 0;
        }

        /* Cambiador de vistas superior (Pestañas) */
        .view-switcher {
            display: flex;
            background-color: #f1f5f9;
            padding: 0.25rem;
            border-radius: 9999px;
            border: 1px solid #e2e8f0;
        }

        .view-switcher__tab {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
            text-decoration: none;
            border-radius: 9999px;
            transition: all 0.2s ease;
        }

        .view-switcher__tab--active {
            background-color: #ffffff;
            color: #bc4685;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Tabla y Contenedores */
        .registry-card {
            padding: 0;
            overflow: hidden;
        }

        .registry-table-wrapper {
            overflow-x: auto;
            width: 100%;
        }

        .registry-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }

        .registry-table th {
            background-color: #f8fafc;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
        }

        .registry-table__row {
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.2s;
        }

        .registry-table__row:hover {
            background-color: #f8fafc;
        }

        .registry-table__td {
            padding: 1rem 1.5rem;
            color: #334155;
            vertical-align: middle;
        }

        .registry-table__td--bold {
            font-weight: 600;
            color: #1e293b;
        }

        .registry-table__td--usd {
            font-weight: 600;
            color: #475569;
        }

        .registry-table__td--bs {
            font-weight: 600;
            color: #bc4685;
        }

        .text-muted {
            color: #94a3b8;
        }

        /* Badges de métodos de pago */
        .badge {
            display: inline-flex;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
            background-color: #f1f5f9;
            color: #475569;
        }

        .badge--info {
            background-color: rgba(188, 70, 133, 0.08);
            color: #bc4685;
        }

        /* Estado vacío */
        .registry-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 5rem 1rem;
            color: #94a3b8;
        }

        .registry-empty__icon {
            width: 3.5rem;
            height: 3.5rem;
            margin-bottom: 1rem;
            opacity: 0.6;
        }



        /* ==========================================================================
                                                           Ajustes Responsivos Actualizados
                                                           ========================================================================== */
        @media (max-width: 992px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.25rem;
            }

            .page-header__actions {
                width: 100%;
                flex-direction: column-reverse;
                /* Mandará los botones abajo y las pestañas arriba en móvil */
                align-items: stretch;
                gap: 1rem;
            }

            .header-actions {
                width: 100%;
            }

            .btn-action {
                flex: 1;
                justify-content: center;
            }

            .view-switcher {
                width: 100%;
            }

            .view-switcher__tab {
                flex: 1;
                justify-content: center;
            }
        }

        /* Contenedor principal de acciones del header */
        .page-header__actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Bloque BEM: Botones de Acción */
        .header-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: var(--radius-full);
            border: 1px solid var(--color-slate-100);
            background-color: var(--color-white);
            color: var(--color-slate-600);
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        /* Modificadores BEM */
        .btn-action:hover {
            box-shadow: var(--shadow-sm);
            color: var(--color-slate-800);
        }

        .btn-action--print:hover {
            border-color: var(--color-secondary);
            color: var(--color-secondary);
            background-color: var(--color-purple-bg);
        }

        .btn-action--clear {
            border-color: rgba(184, 70, 84, 0.2);
            color: var(--color-terracotta);
        }

        .btn-action--clear:hover {
            border-color: var(--color-terracotta);
            background-color: rgba(184, 70, 84, 0.05);
            color: var(--color-terracotta);
        }

        .data-table__footer {
            padding: 1rem 1.5rem;
            background-color: var(--color-slate-50);
            border-top: 1px solid var(--color-slate-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .data-table__footer-text {
            font-size: 0.875rem;
            color: var(--color-slate-500);
        }

        .data-table__footer-highlight {
            font-weight: 600;
            color: var(--color-slate-800);
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination__btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.2rem;
            height: 2.2rem;
            border: 1px solid var(--color-slate-100);
            border-radius: var(--radius-sm);
            background-color: var(--color-white);
            color: var(--color-slate-600);
            cursor: pointer;
            transition: var(--transition-smooth);
            text-decoration: none;
        }

        .pagination__btn:hover:not(.pagination__btn--disabled) {
            border-color: var(--color-primary-light);
            background-color: var(--color-purple-bg);
            color: var(--color-primary);
        }

        .pagination__btn--disabled {
            background-color: var(--color-slate-50);
            color: var(--color-slate-300);
            cursor: not-allowed;
            border-color: var(--color-slate-100);
        }

        .pagination__btn i {
            width: 1.1rem;
            height: 1.1rem;
        }

        .pagination__pages {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .pagination__link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.2rem;
            height: 2.2rem;
            padding: 0 0.5rem;
            border-radius: var(--radius-sm);
            color: var(--color-slate-600);
            font-weight: 500;
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition-smooth);
        }

        .pagination__link:hover:not(.pagination__link--active) {
            background-color: var(--color-slate-100);
            color: var(--color-slate-800);
        }

        .pagination__link--active {
            background-color: var(--color-primary);
            color: var(--color-white);
            font-weight: 600;
        }
    </style>

    <script>
        // 1. Inicialización de iconos de Lucide al cargar la página
        document.addEventListener("DOMContentLoaded", () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        /**
         * 2. Función Global: Interceptor seguro para el modal de confirmación.
         * Al dejarla fuera de eventos, el atributo 'onclick="abrirModalVaciarHistorial()"' del botón la leerá perfectamente.
         */
        function abrirModalVaciarHistorial() {
            // Desparamos tu modal nativo con estética Terracota para advertencias
            if (typeof mostrarNotificacion === 'function') {
                mostrarNotificacion(
                    "¿Vaciar Historial?",
                    "¿Estás absolutamente seguro de eliminar todo el historial de ventas? Esta acción reiniciará los códigos de factura y no se puede deshacer.",
                    "terracotta",
                    "alert-triangle"
                );
            }

            // Inyectamos los botones personalizados de Cancelar y Eliminar al contenedor global
            const accionesBox = document.getElementById('notificacion-acciones');
            if (accionesBox) {
                accionesBox.innerHTML = `
                    <div style="display: flex; gap: 1rem; width: 100%; justify-content: center; margin-top: 1rem;">
                        <button type="button" onclick="cerrarNotificacion()" class="modal__btn" style="background-color: #000; color: #475569; border: 1px solid #cbd5e1; margin: 0;">
                            Cancelar
                        </button>
                        <button type="button" onclick="document.getElementById('form-vaciar-historial').submit()" class="modal__btn" style="background-color: var(--color-terracotta, #e06666); color: white; margin: 0;">
                            Sí, Eliminar
                        </button>
                    </div>
                `;
            }

            // Forzar renderizado de iconos si metes estilos interactivos adicionales
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // 3. Función para generar el PDF utilizando html2pdf.js
        function generarReportePDF() {
            // 1. Clonamos la tarjeta que contiene la tabla para no alterar la vista del usuario
            const tarjetaOriginal = document.querySelector('.pos-card');
            const clon = tarjetaOriginal.cloneNode(true);

            // 2. Quitamos la paginación/footer en el clon para que no salga en el PDF
            const footer = clon.querySelector('.data-table__footer');
            if (footer) footer.remove();

            // 3. Creamos un contenedor temporal en memoria y metemos el encabezado personalizado junto con la tabla clonada
            const elementoAEmitir = document.createElement('div');
            const encabezadoHtml = document.getElementById('encabezado-pdf').innerHTML;

            elementoAEmitir.innerHTML = `
        <div style="padding: 20px; background: #fff;">
            ${encabezadoHtml}
            <div style="margin-top: 10px;">
                ${clon.innerHTML}
            </div>
        </div>
    `;

            // 4. Configuración de la librería html2pdf
            const vistaActual = "{{ $vista }}";
            const fecha = new Date().toISOString().slice(0, 10);

            const opciones = {
                margin: 0.4,
                filename: `reporte_ventas_${vistaActual}_${fecha}.pdf`,
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true
                },
                jsPDF: {
                    unit: 'in',
                    format: 'letter',
                    orientation: 'landscape'
                } // Horizontal da más espacio para las tablas
            };

            // 5. Ejecutar y descargar
            html2pdf().set(opciones).from(elementoAEmitir).save();
        }
    </script>

    <script src="{{ asset('js/html2pdf.js') }}"></script>
@endsection
