@extends('layouts.app')

@section('title', 'HOME - PostresK&G')

@section('content')
    <!-- CUERPO PRINCIPAL DEL DASHBOARD -->
    <div class="app__content">

        <!-- card para actualizar la taza del dolar -->
        <div class="pos-card exchange-card">

            <div class="exchange-card__info">
                <div class="exchange-card__header">
                    <div class="metric-card__icon-box metric-card__icon-box--primary">
                        <i data-lucide="dollar-sign" class="metric-card__icon"></i>
                    </div>
                    <div>
                        <span class="metric-card__label">Moneda Extranjera</span>
                        <h3 class="exchange-card__title">Tasa del Dólar (BCV)</h3>
                    </div>
                </div>
                <p class="exchange-card__description">
                    Define el valor de conversión en Bolívares (Bs.) para sincronizar los totales y calcular vueltos en
                    caja.
                </p>
            </div>

            <div class="exchange-card__action">
                <form action="{{ route('tasa.update') }}" method="POST" class="exchange-form">
                    @csrf
                    @method('PUT')

                    <div class="exchange-form__group">
                        <span class="exchange-form__currency">Bs.</span>
                        <input type="number" step="0.01" name="tasa_bcv" id="tasa-bcv-input"
                            class="exchange-form__input" placeholder="0.00"
                            value="{{ number_format($tasaActiva, 2, '.', '') }}" required>

                        <button type="submit" class="exchange-form__btn">
                            <i data-lucide="save" style="width: 1rem; height: 1rem;"></i>
                            <span>Guardar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SECCIÓN NUEVA: ACCESOS DIRECTOS DE GESTIÓN DE VENTAS -->
        <div class="sales-management-grid">

            <!-- Tarjeta 1: Ver Detalles de Ventas -->
            <div class="pos-card action-shortcut-card">
                <div class="action-shortcut-card__header">
                    <div class="metric-card__icon-box metric-card__icon-box--secondary">
                        <i data-lucide="bar-chart-3" class="metric-card__icon"></i>
                    </div>
                    <div>
                        <h3 class="action-shortcut-card__title">Ver detalles de las ventas</h3>
                        <p class="action-shortcut-card__description">
                            Analiza informes globales, métricas de rendimiento financiero y resúmenes estadísticos de
                            facturación.
                        </p>
                    </div>
                </div>
                <div class="action-shortcut-card__footer">
                    <!-- Cambio aquí: Apunta a la vista de detalles -->
                    <a href="{{ route('ventas.registro', ['vista' => 'detalles']) }}" class="action-shortcut-card__btn">
                        <span>Explorar Detalles</span>
                        <i data-lucide="arrow-right" style="width: 1rem; height: 1rem;"></i>
                    </a>
                </div>
            </div>

            <!-- Tarjeta 2: Ver Ventas Individuales -->
            <div class="pos-card action-shortcut-card">
                <div class="action-shortcut-card__header">
                    <div class="metric-card__icon-box metric-card__icon-box--accent">
                        <i data-lucide="receipt" class="metric-card__icon"></i>
                    </div>
                    <div>
                        <h3 class="action-shortcut-card__title">Ver Ventas individuales</h3>
                        <p class="action-shortcut-card__description">
                            Accede al historial cronológico de facturas emitidas, desglose de artículos y métodos de pago de
                            cada cliente.
                        </p>
                    </div>
                </div>
                <div class="action-shortcut-card__footer">
                    <!-- Cambio aquí: Apunta a la vista de individuales -->
                    <a href="{{ route('ventas.registro', ['vista' => 'individuales']) }}" class="action-shortcut-card__btn">
                        <span>Ver Historial</span>
                        <i data-lucide="arrow-right" style="width: 1rem; height: 1rem;"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- ESTILOS ADICIONALES PARA LA NUEVA SECCIÓN -->
    <style>
        .sales-management-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(45%, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
            width: 100%;
        }

        .action-shortcut-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.75rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .action-shortcut-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.04);
        }

        .action-shortcut-card__header {
            display: flex;
            align-items: flex-start;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .action-shortcut-card__title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
        }

        .action-shortcut-card__description {
            font-size: 0.875rem;
            color: #64748b;
            line-height: 1.5;
        }

        .action-shortcut-card__footer {
            display: flex;
            justify-content: flex-end;
            width: 100%;
        }

        .action-shortcut-card__btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.5rem;
            background: linear-gradient(135deg, #bc4685 0%, #a2336e 100%);
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 9999px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 2px 4px rgba(188, 70, 133, 0.15);
        }

        .action-shortcut-card__btn:hover {
            opacity: 0.95;
            box-shadow: 0 4px 12px rgba(188, 70, 133, 0.25);
        }

        /* Ajuste responsivo para pantallas pequeñas */
        @media (max-width: 768px) {
            .sales-management-grid {
                grid-template-columns: 1fr;
            }

            .action-shortcut-card__header {
                flex-direction: column;
            }
        }
    </style>
@endsection
