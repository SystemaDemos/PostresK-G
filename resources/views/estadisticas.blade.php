@extends('layouts.app')

@section('title', 'Estadísticas - PostresK&G')

@section('content')
    <div class="app__content">

        <div class="metrics-grid">
            <div class="pos-card metric-card">
                <div class="metric-card__content">
                    <span class="metric-card__label">Ingresos de Hoy</span>
                    <h3 id="metric-ingresos" class="metric-card__value">${{ number_format($ingresosHoy, 2) }}</h3>
                    <span
                        class="metric-card__trend {{ $tendenciaAyer >= 0 ? 'metric-card__trend--up' : 'metric-card__trend--down' }}">
                        <i data-lucide="{{ $tendenciaAyer >= 0 ? 'trending-up' : 'trending-down' }}"
                            class="metric-card__trend-icon"></i>
                        {{ $tendenciaAyer >= 0 ? '+' : '' }}{{ number_format($tendenciaAyer, 1) }}% vs ayer
                    </span>
                </div>
                <div class="metric-card__icon-box metric-card__icon-box--primary">
                    <i data-lucide="dollar-sign" class="metric-card__icon"></i>
                </div>
            </div>

            <div class="pos-card metric-card">
                <div class="metric-card__content">
                    <span class="metric-card__label">Transacciones</span>
                    <h3 id="metric-transacciones" class="metric-card__value">{{ $transaccionesHoy }}</h3>
                    <span class="metric-card__trend metric-card__trend--up">
                        <i data-lucide="trending-up" class="metric-card__trend-icon"></i> Hoy
                    </span>
                </div>
                <div class="metric-card__icon-box metric-card__icon-box--secondary">
                    <i data-lucide="credit-card" class="metric-card__icon"></i>
                </div>
            </div>

            <div class="pos-card metric-card">
                <div class="metric-card__content">
                    <span class="metric-card__label">Ticket Promedio</span>
                    <h3 class="metric-card__value">${{ number_format($ticketPromedioSemana, 2) }}</h3>
                    <span class="metric-card__trend metric-card__trend--up">
                        <i data-lucide="activity" class="metric-card__trend-icon"></i> Últimos 7 días
                    </span>
                </div>
                <div class="metric-card__icon-box metric-card__icon-box--accent">
                    <i data-lucide="shopping-cart" class="metric-card__icon"></i>
                </div>
            </div>

            <div class="pos-card metric-card">
                <div class="metric-card__content">
                    <span class="metric-card__label">Bajo Stock</span>
                    <h3 class="metric-card__value">{{ $productosCriticos }}
                        {{ $productosCriticos === 1 ? 'Ítems' : 'Ítems' }}</h3>
                    <span class="metric-card__trend metric-card__trend--warning">
                        <i data-lucide="alert-triangle" class="metric-card__trend-icon"></i> Alerta de almacén
                    </span>
                </div>
                <div class="metric-card__icon-box metric-card__icon-box--terracotta">
                    <i data-lucide="package-x" class="metric-card__icon"></i>
                </div>
            </div>
        </div>

        <div class="analytics-grid">

            <div class="pos-card chart-card analytics-grid__main-chart">
                <div class="chart-card__header">
                    <div class="chart-card__title-box">
                        <h4 class="chart-card__title">Evolución de Ventas</h4>
                        <p class="chart-card__subtitle">Desempeño de ingresos en los últimos 7 días</p>
                    </div>
                    <div class="chart-card__actions">
                        <span class="chart-card__badge chart-card__badge--success">En vivo</span>
                    </div>
                </div>
                <div class="chart-card__body">
                    <div style="position: relative; height: 220px; width: 100%;">
                        <canvas id="ventasSemanaChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="pos-card chart-card analytics-grid__ranking">
                <div class="chart-card__header">
                    <div class="chart-card__title-box">
                        <h4 class="chart-card__title">Top Productos</h4>
                        <p class="chart-card__subtitle">Los más solicitados del mes</p>
                    </div>
                </div>
                <div class="chart-card__body">
                    <div class="top-products">
                        @forelse($topProductos as $index => $prod)
                            @php
                                $barClasses = ['--primary', '--accent', '--secondary'];
                                $claseColor = $barClasses[$index] ?? '--primary';
                                $maxVendido = $topProductos->first()->total_vendido ?? 1;
                                $porcentaje = ($prod->total_vendido / $maxVendido) * 100;
                            @endphp
                            <div class="top-products__item">
                                <div class="top-products__info">
                                    <span class="top-products__name">{{ $prod->nombre }}</span>
                                    <span class="top-products__qty">{{ (int) $prod->total_vendido }} uds.</span>
                                </div>
                                <div class="top-products__bar-container">
                                    <div class="top-products__bar top-products__bar{{ $claseColor }}"
                                        style="width: {{ $porcentaje }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <p class="chart-card__subtitle">No hay ventas registradas este mes.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="pos-card chart-card analytics-grid__action-card">
                <div class="chart-card__header">
                    <div class="chart-card__title-box">
                        <h4 class="chart-card__title">Reportes Automatizados</h4>
                        <p class="chart-card__subtitle">Exportación y acciones rápidas</p>
                    </div>
                </div>
                <div class="chart-card__body chart-card__body--actions">
                    <button class="analytics-btn analytics-btn--primary" onclick="exportarDashboardPDF()">
                        <i data-lucide="file-text" class="analytics-btn__icon"></i>
                        <span class="analytics-btn__text">Generar Reporte de Cierre</span>
                    </button>
                </div>
            </div>

            <!-- Nueva Tarjeta: Rendimiento y Objetivos Diarios -->
            <div class="pos-card chart-card analytics-grid__payment-methods">
                <div class="chart-card__header">
                    <div class="chart-card__title-box">
                        <h4 class="chart-card__title">Objetivos del Día</h4>
                        <p class="chart-card__subtitle">Rendimiento y eficiencia de la terminal hoy</p>
                    </div>
                </div>
                <div class="chart-card__body" style="gap: 1.25rem;">

                    <!-- Meta de Ventas Diario -->
                    <div class="top-products__item">
                        <div class="top-products__info">
                            <span class="top-products__name">Meta de Venta Diaria
                                (${{ number_format($metaDiariaUSD, 2) }})</span>
                            <span class="top-products__qty">{{ number_format($porcentajeMeta, 1) }}%</span>
                        </div>
                        <div class="top-products__bar-container"
                            style="height: 10px; background-color: var(--color-slate-100);">
                            <div class="top-products__bar"
                                style="width: {{ $porcentajeMeta }}%; background-color: #10b981;"></div>
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px dashed var(--color-slate-100); margin: 0.25rem 0;">

                    <!-- Bloque de Estadísticas de Compra -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">

                        <div
                            style="background: var(--color-bg); padding: 0.85rem; border-radius: var(--radius-md); text-align: center; border: 1px solid var(--color-slate-50);">
                            <i data-lucide="shopping-bag"
                                style="width: 1.25rem; height: 1.25rem; color: var(--color-primary); margin-bottom: 0.25rem;"></i>
                            <span
                                style="display: block; font-size: 0.75rem; color: var(--color-slate-500); font-weight: 600;">Artículos
                                / Ticket</span>
                            <strong
                                style="font-size: 1.25rem; color: var(--color-slate-800); display: block; margin-top: 0.25rem;">
                                {{ number_format($productosPorTicket, 1) }} u.
                            </strong>
                        </div>

                        <div
                            style="background: var(--color-bg); padding: 0.85rem; border-radius: var(--radius-md); text-align: center; border: 1px solid var(--color-slate-50);">
                            <i data-lucide="zap"
                                style="width: 1.25rem; height: 1.25rem; color: var(--color-accent); margin-bottom: 0.25rem;"></i>
                            <span
                                style="display: block; font-size: 0.75rem; color: var(--color-slate-500); font-weight: 600;">Estado
                                de Caja</span>
                            <strong
                                style="font-size: 0.9rem; color: #10b981; display: block; margin-top: 0.5rem; text-transform: uppercase; font-weight: 700;">
                                {{ $ingresosHoy > 0 ? 'Activa' : 'Sin Ventas' }}
                            </strong>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

    <style>
        /* ==========================================================================
                                                   X. SECCIÓN DE ESTADÍSTICAS Y ANALÍTICAS (BEM)
                                                   ========================================================================== */
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1.5rem;
            margin-top: 1.5rem;
            padding-bottom: 2rem;
        }

        .analytics-grid__main-chart {
            grid-column: span 8;
        }

        .analytics-grid__ranking {
            grid-column: span 4;
        }

        .analytics-grid__action-card {
            grid-column: span 5;
        }

        .analytics-grid__payment-methods {
            grid-column: span 7;
        }

        .chart-card {
            background-color: var(--color-white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            border: 1px solid var(--color-slate-50);
            transition: var(--transition-smooth);
        }

        .chart-card:hover {
            box-shadow: var(--shadow-lg);
        }

        .chart-card__header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .chart-card__title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--color-slate-800);
        }

        .chart-card__subtitle {
            font-size: 0.85rem;
            color: var(--color-slate-500);
            margin-top: 0.15rem;
        }

        .chart-card__badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-full);
        }

        .chart-card__badge--success {
            background-color: var(--color-emerald-bg);
            color: var(--color-emerald-text);
        }

        .chart-card__body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .chart-card__body--actions {
            gap: 0.75rem;
        }

        .chart-card__placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            height: 180px;
            background-color: var(--color-bg);
            border: 2px dashed var(--color-slate-100);
            border-radius: var(--radius-md);
            color: var(--color-slate-400);
            font-size: 0.9rem;
        }

        .chart-card__placeholder-icon {
            width: 2.5rem;
            height: 2.5rem;
            stroke-width: 1.5;
            color: var(--color-primary-light);
        }

        .top-products {
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
        }

        .top-products__item {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .top-products__info {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
        }

        .top-products__name {
            font-weight: 600;
            color: var(--color-slate-700);
        }

        .top-products__qty {
            font-weight: 700;
            color: var(--color-slate-800);
        }

        .top-products__bar-container {
            width: 100%;
            height: 8px;
            background-color: var(--color-slate-50);
            border-radius: var(--radius-full);
            overflow: hidden;
        }

        .top-products__bar {
            height: 100%;
            border-radius: var(--radius-full);
            transition: width 1s ease-in-out;
        }

        .top-products__bar--primary {
            background-color: var(--color-primary);
        }

        .top-products__bar--accent {
            background-color: var(--color-accent);
        }

        .top-products__bar--secondary {
            background-color: var(--color-secondary);
        }

        .analytics-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: var(--radius-md);
            cursor: pointer;
            border: 1px solid transparent;
            transition: var(--transition-smooth);
            width: 100%;
        }

        .analytics-btn__icon {
            width: 1.1rem;
            height: 1.1rem;
        }

        .analytics-btn--primary {
            background-color: var(--color-accent);
            color: var(--color-white);
        }

        .analytics-btn--primary:hover {
            background-color: #a0287f;
            box-shadow: var(--shadow-accent);
        }

        .analytics-btn--outline {
            background-color: transparent;
            border-color: var(--color-slate-100);
            color: var(--color-slate-600);
        }

        .analytics-btn--outline:hover {
            background-color: var(--color-slate-50);
            color: var(--color-primary);
            border-color: var(--color-primary-light);
        }

        @media (max-width: 1024px) {

            .analytics-grid__main-chart,
            .analytics-grid__ranking,
            .analytics-grid__action-card,
            .analytics-grid__payment-methods {
                grid-column: span 12;
            }
        }
    </style>

    <!-- PLANTILLA EXCLUSIVA PARA EL PDF DE ESTADÍSTICAS (Mantenida fuera de la vista web) -->
    <div id="dashboard-pdf-template" style="display: none;">
        <div style="font-family: system-ui, -apple-system, sans-serif; color: #1e293b; padding: 10px; background: #fff;">

            <!-- Encabezado Corporativo -->
            <table
                style="width: 100%; border-bottom: 2px solid #bc4685; padding-bottom: 12px; margin-bottom: 20px; border-collapse: collapse;">
                <tr>
                    <td style="vertical-align: middle;">
                        <h1 style="margin: 0; font-size: 26px; font-weight: 700; color: #1e293b;">Postres<span
                                style="color: #bc4685;">K&G</span></h1>
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: #64748b; font-weight: 500;">Bodega del Materno
                            - Reporte de Rendimiento</p>
                    </td>
                    <td style="text-align: right; vertical-align: middle;">
                        <h2 style="margin: 0; font-size: 14px; font-weight: 700; color: #bc4685; letter-spacing: 0.5px;">
                            REPORTE DE CIERRE ESTADÍSTICO</h2>
                        <p style="margin: 4px 0 0 0; font-size: 11px; color: #475569;"><strong>Emitido:</strong>
                            {{ now()->format('d/m/Y h:i A') }}</p>
                    </td>
                </tr>
            </table>

            <!-- Fila 1: Bloque de las 4 Tarjetas de Métricas -->
            <table style="width: 100%; margin-bottom: 25px; border-collapse: separate; border-spacing: 12px 0;">
                <tr>
                    <!-- Tarjeta 1 -->
                    <td
                        style="width: 25%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                        <span
                            style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 6px;">Ingresos
                            de Hoy</span>
                        <strong id="pdf-ingresos" style="font-size: 20px; color: #1e293b; display: block;">$0.00</strong>
                    </td>
                    <!-- Tarjeta 2 -->
                    <td
                        style="width: 25%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                        <span
                            style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 6px;">Transacciones</span>
                        <strong id="pdf-transacciones" style="font-size: 20px; color: #1e293b; display: block;">0</strong>
                    </td>
                    <!-- Tarjeta 3 -->
                    <td
                        style="width: 25%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                        <span
                            style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 6px;">Ticket
                            Promedio</span>
                        <strong id="pdf-ticket" style="font-size: 20px; color: #1e293b; display: block;">$0.00</strong>
                    </td>
                    <!-- Tarjeta 4 -->
                    <td
                        style="width: 25%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                        <span
                            style="font-size: 11px; color: #475569; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 6px;">Alertas
                            de Stock</span>
                        <strong id="pdf-stock" style="font-size: 20px; color: #ef4444; display: block;">0 Ítems</strong>
                    </td>
                </tr>
            </table>

            <!-- Fila 2: Gráfico Principal de Evolución y Objetivos Diarios -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <!-- Gráfico de Ventas -->
                    <td style="width: 65%; padding-right: 15px; vertical-align: top;">
                        <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; background: #ffffff;">
                            <h3 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #1e293b;">Evolución de
                                Ventas Semanales</h3>
                            <p style="margin: 0 0 15px 0; font-size: 11px; color: #64748b;">Historial gráfico del desempeño
                                de ingresos</p>
                            <div style="text-align: center;">
                                <!-- Espacio donde inyectaremos la captura nítida del gráfico canvas -->
                                <img id="pdf-render-chart" src=""
                                    style="max-width: 100%; height: auto; display: block; margin: 0 auto; border-radius: 4px;" />
                            </div>
                        </div>
                    </td>

                    <!-- Objetivos Diarios y Métricas de Caja -->
                    <td style="width: 35%; vertical-align: top;">
                        <div
                            style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; background: #ffffff; height: 100%;">
                            <h3 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #1e293b;">Objetivos del
                                Día</h3>
                            <p style="margin: 0 0 15px 0; font-size: 11px; color: #64748b;">Rendimiento interno de la
                                terminal de venta</p>

                            <!-- Progreso Meta Diaria -->
                            <div style="margin-bottom: 15px;">
                                <div
                                    style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 600; margin-bottom: 4px;">
                                    <span style="color: #475569;">Cumplimiento Meta Diaria</span>
                                    <span style="color: #10b981;">{{ number_format($porcentajeMeta, 1) }}%</span>
                                </div>
                                <div
                                    style="width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                                    <div style="width: {{ $porcentajeMeta }}%; height: 100%; background: #10b981;"></div>
                                </div>
                            </div>

                            <div style="border-top: 1px dashed #e2e8f0; margin: 15px 0;"></div>

                            <!-- Mini Bloques Informativos -->
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 50%; padding-right: 6px;">
                                        <div
                                            style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 6px; padding: 10px; text-align: center;">
                                            <span
                                                style="font-size: 10px; color: #64748b; font-weight: 600; display: block;">Artículos
                                                / Ticket</span>
                                            <strong
                                                style="font-size: 14px; color: #1e293b; display: block; margin-top: 4px;">{{ number_format($productosPorTicket, 1) }}
                                                u.</strong>
                                        </div>
                                    </td>
                                    <td style="width: 50%; padding-left: 6px;">
                                        <div
                                            style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 6px; padding: 10px; text-align: center;">
                                            <span
                                                style="font-size: 10px; color: #64748b; font-weight: 600; display: block;">Estado
                                                de Caja</span>
                                            <strong
                                                style="font-size: 11px; color: #10b981; display: block; margin-top: 6px; font-weight: 700; text-transform: uppercase;">{{ $ingresosHoy > 0 ? 'Activa' : 'Sin Ventas' }}</strong>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Fila 3: Ranking de Productos más Vendidos -->
            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; background: #ffffff;">
                <h3 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #1e293b;">Top Productos del Mes
                </h3>
                <p style="margin: 0 0 12px 0; font-size: 11px; color: #64748b;">Artículos con mayor demanda en el
                    inventario actual</p>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f1f5f9; text-align: left;">
                            <th
                                style="padding: 6px 10px; font-size: 11px; font-weight: 600; color: #334155; border-radius: 4px 0 0 4px;">
                                Producto</th>
                            <th
                                style="padding: 6px 10px; font-size: 11px; font-weight: 600; color: #334155; text-align: right; border-radius: 0 4px 4px 0;">
                                Unidades Vendidas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProductos as $prod)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 8px 10px; font-size: 11px; color: #1e293b; font-weight: 500;">
                                    {{ $prod->nombre }}</td>
                                <td
                                    style="padding: 8px 10px; font-size: 11px; color: #334155; text-align: right; font-weight: 600;">
                                    {{ (int) $prod->total_vendido }} uds.</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2"
                                    style="padding: 10px; font-size: 11px; color: #64748b; text-align: center;">No hay
                                    transacciones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="{{ asset('js/vendor/chart.umd.js') }}"></script>
    <!-- Asegúrate de que html2pdf.js esté cargado antes de ejecutar la función -->


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const colorPrimary = '#9472B8';
            const colorAccent = '#B83594';
            const colorSecondary = '#7E72B8';
            const colorSlateText = '#475569';
            const colorSlateGrid = '#f1f5f9';

            // ==========================================
            // 1. EXTRACT DATA FROM LARAVEL UNIFIED
            // ==========================================
            const datosSemanalesLaravel = @json($ventasSemanales);
            const metodosPagoLaravel = @json($metodosPago);

            // ==========================================
            // 2. RENDER CHART VENTAS SEMANALES
            // ==========================================
            const labelsVentas = datosSemanalesLaravel.map(item => item.dia);
            const valoresVentas = datosSemanalesLaravel.map(item => item.total);

            const ctxVentas = document.getElementById('ventasSemanaChart').getContext('2d');
            new Chart(ctxVentas, {
                type: 'line',
                data: {
                    labels: labelsVentas,
                    datasets: [{
                        label: 'Ingresos ($)',
                        data: valoresVentas,
                        borderColor: colorPrimary,
                        backgroundColor: 'rgba(148, 114, 184, 0.08)',
                        borderWidth: 3,
                        tension: 0.38,
                        fill: true,
                        pointBackgroundColor: colorPrimary
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: colorSlateText
                            }
                        },
                        y: {
                            grid: {
                                color: colorSlateGrid
                            },
                            ticks: {
                                color: colorSlateText
                            }
                        }
                    }
                }
            });
        });

        // ==========================================
        // 3. EXPORT DASHBOARD TO PDF (FIXED FOR GRAPHICS)
        // ==========================================
        function exportarDashboardPDF() {
            // 1. Extraer los textos y métricas exactas que se muestran actualmente en la web
            const ingresos = document.getElementById('metric-ingresos').innerText;
            const transacciones = document.getElementById('metric-transacciones').innerText;

            // Para el ticket promedio y stock crítico, buscamos los contenedores por clase correspondientes
            const valoresCards = document.querySelectorAll('.metric-card__value');
            const ticketPromedio = valoresCards[2] ? valoresCards[2].innerText : "$0.00";
            const bajoStock = valoresCards[3] ? valoresCards[3].innerText : "0 Ítems";

            // 2. Sincronizar e inyectar estos datos en los nodos correspondientes de nuestra plantilla del PDF
            document.getElementById('pdf-ingresos').innerText = ingresos;
            document.getElementById('pdf-transacciones').innerText = transacciones;
            document.getElementById('pdf-ticket').innerText = ticketPromedio;
            document.getElementById('pdf-stock').innerText = bajoStock;

            // 3. CAPTURA CRÍTICA: Convertir el gráfico dinámico de Chart.js en una imagen estática limpia
            const chartCanvas = document.getElementById('ventasSemanaChart');
            const pdfImageNode = document.getElementById('pdf-render-chart');

            if (chartCanvas && pdfImageNode) {
                // Obtenemos la codificación en base64 del canvas actual en pantalla
                const chartBase64 = chartCanvas.toDataURL('image/png', 1.0);

                // PASO CLAVE CORREGIDO: Esperamos a que la imagen cargue completamente en memoria antes de armar el PDF
                pdfImageNode.onload = function() {
                    ejecutarDescargaPDF();
                };

                // Asignamos el src para disparar el evento onload
                pdfImageNode.src = chartBase64;
            } else {
                // Si por alguna razón el canvas no existe, descargamos el PDF de todas formas sin el gráfico
                ejecutarDescargaPDF();
            }
        }

        // Función interna encargada estrictamente de la compilación del documento con html2pdf
        function ejecutarDescargaPDF() {
            // 4. Clonar el contenedor de la plantilla del PDF para procesarlo en memoria limpia
            const plantillaOriginal = document.getElementById('dashboard-pdf-template');
            const elementoAEmitir = plantillaOriginal.cloneNode(true);
            elementoAEmitir.style.display = 'block'; // Lo hacemos visible en el motor de renderizado de la librería

            // 5. Configurar las propiedades de descarga del PDF en formato Horizontal (Landscape)
            const fechaActual = new Date().toISOString().slice(0, 10);
            const opciones = {
                margin: 0.3,
                filename: `reporte_cierre_caja_${fechaActual}.pdf`,
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    logging: false
                },
                jsPDF: {
                    unit: 'in',
                    format: 'letter',
                    orientation: 'landscape'
                }
            };

            // 6. Lanzar la conversión y procesar la descarga directa del archivo corporativo
            html2pdf().set(opciones).from(elementoAEmitir).save();
        }
    </script>

    <script src="{{ asset('js/html2pdf.js') }}"></script>
@endsection
