@extends('layouts.app')

@section('title', 'Punto de Venta - PostresK&G')

@section('content')
    <div class="app__content">

        <div class="workspace">

            <div class="pos-card pos-panel">
                <div>
                    <div class="pos-panel__header">
                        <div class="pos-panel__title-box">
                            <h3 class="pos-panel__title">Terminal POS - Productos</h3>
                            <p class="pos-panel__subtitle">Escribe para filtrar o haz clic en un producto para añadirlo</p>
                        </div>

                        <div class="pos-search">
                            <div class="pos-search__wrapper">
                                <i data-lucide="search" class="pos-search__icon"></i>
                                <input type="text" id="buscar-producto" class="pos-search__input"
                                    placeholder="Buscar producto por nombre..." autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="products-grid" id="contenedor-productos">

                        @forelse($productos as $producto)
                            <button
                                onclick="agregarProducto({{ $producto->id }}, '{{ addslashes($producto->nombre) }}', {{ $producto->precio_de_venta }}, {{ $producto->cantidad }})"
                                class="product-card" data-nombre="{{ strtolower($producto->nombre) }}"
                                {{ $producto->cantidad <= 0 ? 'disabled style=opacity:0.5;cursor:not-allowed;' : '' }}>

                                <div class="product-card__icon-wrapper product-card__icon-wrapper--primary">
                                    <i data-lucide="package" style="width: 1.25rem; height: 1.25rem;"></i>
                                </div>

                                <span class="product-card__title">{{ $producto->nombre }}</span>

                                <span class="product-card__category">
                                    @if ($producto->cantidad <= 0)
                                        <strong style="color: var(--color-terracotta);">Agotado</strong>
                                    @else
                                        Stock: {{ $producto->cantidad }} u.
                                    @endif
                                </span>

                                <span class="product-card__price">
                                    {{ number_format($producto->precio_de_venta * ($tasaCambio ?? 45.0), 2, ',', '.') }}
                                    Bs.
                                </span>
                            </button>
                        @empty
                            <div
                                style="grid-column: span 3; text-align: center; color: var(--color-slate-400); padding: 3rem 1rem;">
                                <i data-lucide="archive-x"
                                    style="width: 3rem; height: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                                <p>No hay productos disponibles en el inventario para vender.</p>
                            </div>
                        @endforelse

                        <div id="busqueda-vacia"
                            style="display: none; grid-column: span 3; text-align: center; color: #94a3b8; padding: 4rem 1rem;">
                            <i data-lucide="search-x"
                                style="width: 3rem; height: 3rem; margin: 0 auto 1rem; opacity: 0.5;"></i>
                            <p style="font-size: 1.1rem; font-weight: 500;">No se encontraron productos</p>
                            <p style="font-size: 0.875rem; opacity: 0.8;">Intenta con otro término o verifica el inventario.
                            </p>
                        </div>

                    </div>
                </div>
            </div>

            <div class="pos-card terminal">
                <div>
                    <div class="terminal__header">
                        <div>
                            <h3 class="terminal__title">Orden Actual</h3>
                            <p class="terminal__subtitle">Detalle del carrito activo</p>
                        </div>
                        <button onclick="vaciarCarrito()" class="terminal__clear-btn">
                            <i data-lucide="trash-2" style="width: 0.875rem; height: 0.875rem;"></i>
                            <span>Vaciar</span>
                        </button>
                    </div>

                    <div id="lista-carrito" class="cart-list">
                        <div id="carrito-vacio" class="cart-list__empty">
                            <i data-lucide="shopping-cart" class="cart-list__empty-icon"></i>
                            <p class="cart-list__empty-title">El carrito está vacío</p>
                            <p class="cart-list__empty-subtitle">Selecciona productos a la izquierda</p>
                        </div>
                    </div>
                </div>

                <div class="summary">
                    <div class="summary__row">
                        <span>En Dólares ($)</span>
                        <span id="carrito-total-usd" class="summary__val"
                            style="font-weight: 600; color: #475569;">$0.00</span>
                    </div>
                    <div class="summary__row">
                        <span>Tasa Ref.</span>
                        <span class="summary__val" style="color: #94a3b8; font-size: 0.85rem;">
                            {{ number_format($tasaCambio ?? 45.0, 2, ',', '.') }} Bs./$
                        </span>
                    </div>
                    <div class="summary__row summary__row--total">
                        <span>Total</span>
                        <span id="carrito-total-bs" class="summary__val--total">0,00 Bs.</span>
                    </div>

                    <div class="payment-methods">
                        <button onclick="setMetodoPago('Efectivo')" class="payment-methods__btn">
                            <i data-lucide="banknote" class="payment-methods__icon"></i>
                            <span>Efectivo</span>
                        </button>
                        <button onclick="setMetodoPago('Tarjeta')" class="payment-methods__btn">
                            <i data-lucide="credit-card" class="payment-methods__icon"></i>
                            <span>Tarjeta</span>
                        </button>
                        <button onclick="setMetodoPago('NFC')" class="payment-methods__btn">
                            <i data-lucide="smartphone" class="payment-methods__icon"></i>
                            <span>NFC / Móvil</span>
                        </button>
                    </div>

                    <button onclick="procesarPago()" class="terminal__submit-btn">
                        <i data-lucide="check-circle" class="terminal__submit-icon"></i>
                        <span>Completar Venta</span>
                    </button>
                </div>

            </div>

        </div>

    </div>

    <style>
        /* --- COMPONENTE: BUSCADOR POS --- */
        .pos-search__wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .pos-search__icon {
            position: absolute;
            left: 1rem;
            color: #94a3b8;
            width: 1.1rem;
            height: 1.1rem;
            pointer-events: none;
        }

        .pos-search__input {
            width: 280px;
            padding: 0.65rem 1rem 0.65rem 2.6rem;
            border: 1px solid #e2e8f0;
            border-radius: 9999px;
            font-size: 0.875rem;
            color: #334155;
            outline: none;
            background-color: #f8fafc;
            transition: all 0.2s ease-in-out;
        }

        .pos-search__input:focus {
            border-color: #bc4685;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(188, 70, 133, 0.15);
        }

        .product-card--hidden {
            display: none !important;
        }
    </style>

    <script>
        // --- CONFIGURACIÓN E INICIALIZACIÓN GLOBAL ---
        // Inyectamos la tasa de cambio real desde tu controlador de Laravel
        const tasaCambio = {{ $tasaCambio ?? 45.0 }};
        let carrito = [];
        let metodoPagoSeleccionado = "";

        document.addEventListener("DOMContentLoaded", () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            renderCarrito();
            inicializarBuscador();
        });

        // --- SISTEMA DE FILTRADO Y LÍMITE VISUAL DE PRODUCTOS (MÁX 9) ---
        function inicializarBuscador() {
            const inputBuscar = document.getElementById("buscar-producto");
            const tarjetas = document.querySelectorAll(".product-card");
            const busquedaVaciaEl = document.getElementById("busqueda-vacia");

            function aplicarFiltroYLite() {
                const termino = inputBuscar ? inputBuscar.value.toLowerCase().trim() : "";
                let mostrados = 0;
                let coincidenciasTotales = 0;

                tarjetas.forEach((tarjeta) => {
                    const nombreProducto = tarjeta.getAttribute("data-nombre");
                    const coincide = nombreProducto.includes(termino);

                    if (coincide) {
                        coincidenciasTotales++;

                        if (mostrados < 9) {
                            tarjeta.classList.remove("product-card--hidden");
                            mostrados++;
                        } else {
                            tarjeta.classList.add("product-card--hidden");
                        }
                    } else {
                        tarjeta.classList.add("product-card--hidden");
                    }
                });

                if (busquedaVaciaEl) {
                    busquedaVaciaEl.style.display = (coincidenciasTotales === 0 && termino !== "") ? "block" : "none";
                }
            }

            if (inputBuscar) {
                inputBuscar.addEventListener("input", aplicarFiltroYLite);
            }

            aplicarFiltroYLite();
        }

        // --- LÓGICA INTERACTIVA DEL CARRITO DE COMPRAS ---
        function agregarProducto(id, nombre, precio, stockMaximo) {
            const itemExistente = carrito.find((item) => item.id === id);

            if (itemExistente) {
                if (itemExistente.cantidad >= stockMaximo) {
                    mostrarNotificacion("Stock Límite", `Solo quedan ${stockMaximo} unidades de "${nombre}" en inventario.`,
                        "terracotta", "alert-circle");
                    return;
                }
                itemExistente.cantidad += 1;
            } else {
                if (stockMaximo <= 0) {
                    mostrarNotificacion("Producto Agotado", `"${nombre}" se encuentra agotado.`, "terracotta",
                        "alert-circle");
                    return;
                }
                carrito.push({
                    id,
                    nombre,
                    precio: parseFloat(precio),
                    cantidad: 1,
                    stockMaximo
                });
            }
            renderCarrito();
        }

        function cambiarCantidad(id, cambio) {
            const item = carrito.find((item) => item.id === id);

            if (item) {
                if (cambio > 0 && item.cantidad >= item.stockMaximo) {
                    mostrarNotificacion("Límite alcanzado",
                        `No puedes añadir más. El inventario máximo es de ${item.stockMaximo} u.`, "terracotta",
                        "alert-triangle");
                    return;
                }

                item.cantidad += cambio;

                if (item.cantidad <= 0) {
                    carrito = carrito.filter((i) => i.id !== id);
                }
            }
            renderCarrito();
        }

        function vaciarCarrito() {
            carrito = [];
            metodoPagoSeleccionado = "";
            document.querySelectorAll(".payment-methods__btn").forEach((el) => {
                el.classList.remove("payment-methods__btn--active");
                el.style.borderColor = "";
            });
            renderCarrito();
        }

        function renderCarrito() {
            const listaEl = document.getElementById("lista-carrito");
            const vacioEl = document.getElementById("carrito-vacio");

            if (!listaEl) return;

            listaEl.innerHTML = "";

            if (carrito.length === 0) {
                if (vacioEl) {
                    vacioEl.style.display = "flex";
                    listaEl.appendChild(vacioEl);
                }
                actualizarTotales(0);
                return;
            }

            if (vacioEl) vacioEl.style.display = "none";

            carrito.forEach((item) => {
                const row = document.createElement("div");
                row.className = "cart-item";

                const precioEnBs = item.precio * tasaCambio;
                const subtotalItemEnBs = precioEnBs * item.cantidad;

                row.innerHTML = `
                    <div class="cart-item__left-box">
                        <div class="cart-item__icon-wrapper">
                            <i data-lucide="package" class="cart-item__icon"></i>
                        </div>
                        <div>
                            <h5 class="cart-item__title">${item.nombre}</h5>
                            <p class="cart-item__price">${precioEnBs.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} Bs. c/u</p>
                        </div>
                    </div>
                    <div class="cart-item__right-box">
                        <div class="qty-control">
                            <button onclick="cambiarCantidad(${item.id}, -1)" class="qty-control__btn">-</button>
                            <span class="qty-control__value">${item.cantidad}</span>
                            <button onclick="cambiarCantidad(${item.id}, 1)" class="qty-control__btn">+</button>
                        </div>
                        <span class="cart-item__total">${subtotalItemEnBs.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} Bs.</span>
                    </div>
                `;
                listaEl.appendChild(row);
            });

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            const totalUSD = carrito.reduce((acc, current) => acc + (current.precio * current.cantidad), 0);
            actualizarTotales(totalUSD);
        }

        function actualizarTotales(totalUsd) {
            const totalBs = totalUsd * tasaCambio;

            const totalUsdEl = document.getElementById("carrito-total-usd");
            const totalBsEl = document.getElementById("carrito-total-bs");

            if (totalUsdEl) {
                totalUsdEl.textContent = `$${totalUsd.toFixed(2)}`;
            }

            if (totalBsEl) {
                totalBsEl.textContent =
                    `${totalBs.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} Bs.`;
            }
        }

        function setMetodoPago(metodo) {
            metodoPagoSeleccionado = metodo;

            document.querySelectorAll(".payment-methods__btn").forEach((btn) => {
                btn.classList.remove("payment-methods__btn--active");
                btn.style.borderColor = "";
            });

            const botones = document.querySelectorAll(".payment-methods__btn");
            botones.forEach((btn) => {
                if (btn.innerText.includes(metodo)) {
                    btn.classList.add("payment-methods__btn--active");
                    btn.style.borderColor = "var(--color-primary, #bc4685)";
                }
            });
        }

        // --- PROCESAR PAGO CON CONEXIÓN EN BASE DE DATOS ---
        function procesarPago() {
            if (carrito.length === 0) {
                mostrarNotificacion("Operación Inválida", "El carrito está vacío.", "terracotta", "alert-circle");
                return;
            }

            if (!metodoPagoSeleccionado) {
                mostrarNotificacion("Método de Pago", "Por favor seleccione Efectivo, Tarjeta o NFC.", "terracotta",
                    "credit-card");
                return;
            }

            const totalUsd = carrito.reduce((acc, current) => acc + (current.precio * current.cantidad), 0);
            const totalBs = totalUsd * tasaCambio;

            // Formamos el objeto JSON tal cual lo procesará el backend en Laravel
            const datosVenta = {
                cliente_nombre: 'Cliente General',
                metodo_pago: metodoPagoSeleccionado,
                tasa_cambio: tasaCambio,
                total_usd: totalUsd,
                total_bs: totalBs,
                carrito: carrito
            };

            fetch("{{ route('pos.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(datosVenta)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Error en la respuesta del servidor");
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        mostrarNotificacion("¡Venta Exitosa!", data.message, "accent", "check");
                        vaciarCarrito();

                        // Pequeño delay de 2.5 segundos para que alcances a leer la ventana de confirmación antes de recargar la grilla
                        setTimeout(() => {
                            location.reload();
                        }, 2500);
                    } else {
                        mostrarNotificacion("Error de Guardado", data.message, "terracotta", "alert-triangle");
                    }
                })
                .catch(error => {
                    console.error("Error de comunicación:", error);
                    mostrarNotificacion("Error Crítico", "No se pudo establecer comunicación con el servidor.",
                        "terracotta", "wifi-off");
                });
        }
    </script>
@endsection
