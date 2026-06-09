@extends('layouts.app')

@section('title', 'Inventario de Productos - PostresK&G')

@section('content')
    <div class="app__content">

        <header class="page-header">
            <h2 class="page-header__title">Inventario de Productos</h2>
            <p class="page-header__subtitle">Consulta, edita o elimina los productos registrados en el sistema POS.</p>
        </header>

        <div class="data-table">

            <div class="data-table__toolbar">
                <form action="{{ route('productos.index') }}" method="GET" class="data-table__search-box" id="form-buscador">
                    <i data-lucide="search" class="data-table__search-icon"></i>
                    <input type="text" name="buscar" id="inventario-buscar" class="data-table__search-input"
                        placeholder="Buscar producto..." value="{{ $buscar ?? '' }}">

                    <input type="hidden" name="columna" value="{{ $columna ?? 'id' }}">
                    <input type="hidden" name="orden" value="{{ $orden ?? 'asc' }}">
                </form>
            </div>

            <div class="data-table__wrapper">
                <table class="data-table__main" id="tabla-productos">

                    <thead class="data-table__head">
                        <tr class="data-table__row data-table__row--header">
                            <th class="data-table__th data-table__th--sortable" data-columna="0" data-type="number"
                                data-slug="id">
                                <span>ID</span>
                                <i data-lucide="arrow-up-down" class="data-table__sort-icon"></i>
                            </th>
                            <th class="data-table__th data-table__th--sortable" data-columna="1" data-type="text"
                                data-slug="nombre">
                                <span>Nombre del Producto</span>
                                <i data-lucide="arrow-up-down" class="data-table__sort-icon"></i>
                            </th>
                            <th class="data-table__th data-table__th--sortable data-table__th--center" data-columna="2"
                                data-type="number" data-slug="cantidad">
                                <span>Cantidad</span>
                                <i data-lucide="arrow-up-down" class="data-table__sort-icon"></i>
                            </th>
                            <th class="data-table__th" data-columna="3" data-type="text">
                                <span>Categoría</span>
                            </th>
                            <th class="data-table__th data-table__th--sortable data-table__th--right" data-columna="4"
                                data-type="number" data-slug="precio">
                                <span>Valor de Venta</span>
                                <i data-lucide="arrow-up-down" class="data-table__sort-icon"></i>
                            </th>
                            <th class="data-table__th data-table__th--center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="data-table__body">
                        @forelse($productos as $producto)
                            <tr class="data-table__row">
                                <td class="data-table__td data-table__td--bold">#{{ sprintf('%03d', $producto->id) }}</td>
                                <td class="data-table__td data-table__product-name">{{ $producto->nombre }}</td>

                                <td class="data-table__td data-table__td--center" data-valor="{{ $producto->cantidad }}">
                                    <span
                                        class="data-table__stock {{ $producto->cantidad <= 5 ? 'data-table__stock--low' : 'data-table__stock--in' }}">
                                        {{ (float) $producto->cantidad == (int) $producto->cantidad ? number_format($producto->cantidad, 0) : number_format($producto->cantidad, 3, ',', '.') }}
                                        u.
                                    </span>
                                </td>

                                <td class="data-table__td data-table__product-name">
                                    {{ $producto->categoria?->nombre ?? 'Sin Categoría' }}
                                </td>

                                <td class="data-table__td data-table__td--right"
                                    data-valor="{{ $producto->precio_de_venta }}">
                                    ${{ number_format($producto->precio_de_venta, 2) }}

                                    <span style="color: #008311; font-size: 0.95rem; margin-left: 4px;">
                                        / {{ number_format($producto->precio_de_venta * $tasaActiva, 2, '.', '') }} bs
                                    </span>
                                </td>

                                <td class="data-table__td data-table__td--center">
                                    <div class="data-table__actions">
                                        <a href="{{ route('productos.edit', $producto->id) }}"
                                            class="data-table__btn data-table__btn--edit" title="Editar Producto">
                                            <i data-lucide="pencil"></i>
                                        </a>
                                        <form id="form-eliminar-{{ $producto->id }}"
                                            action="{{ route('productos.destroy', $producto->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="data-table__btn data-table__btn--delete"
                                                title="Eliminar Producto" onclick="abrirModalConfirmacion(this)"
                                                data-id="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}">
                                                <i data-lucide="trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="data-table__row">
                                <td colspan="6" class="data-table__td data-table__td--center"
                                    style="color: var(--color-slate-400); padding: 2rem;">
                                    No hay productos registrados en el inventario.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="data-table__footer">
                <p class="data-table__footer-text">
                    Mostrando <span
                        class="data-table__footer-highlight">{{ method_exists($productos, 'firstItem') ? $productos->firstItem() ?? 0 : 1 }}</span>
                    al <span
                        class="data-table__footer-highlight">{{ method_exists($productos, 'lastItem') ? $productos->lastItem() ?? 0 : count($productos) }}</span>
                    de <span id="tabla-conteo-filas"
                        class="data-table__footer-highlight">{{ method_exists($productos, 'total') ? $productos->total() : count($productos) }}</span>
                    productos
                </p>

                {{-- Comprobamos de manera segura si el objeto es un paginador y si tiene páginas --}}
                @if (method_exists($productos, 'hasPages') && $productos->hasPages())
                    <nav class="pagination">
                        {{-- Botón Anterior --}}
                        @if ($productos->onFirstPage())
                            <span class="pagination__btn pagination__btn--disabled">
                                <i data-lucide="chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $productos->previousPageUrl() }}" class="pagination__btn">
                                <i data-lucide="chevron-left"></i>
                            </a>
                        @endif

                        {{-- Números de Páginas con Ventana Inteligente (...) Limpia --}}
                        <div class="pagination__pages">
                            {{-- onEachSide(1) le dice a Laravel que pinte solo 1 número a los lados de la página actual y ponga "..." en el resto --}}
                            @foreach ($productos->onEachSide(1)->linkCollection() as $link)
                                {{-- Si es el botón de anterior o siguiente del objeto nativo, lo ignoramos porque ya tenemos los tuyos --}}
                                @if (!str_contains($link['label'], 'Previous') && !str_contains($link['label'], 'Next'))
                                    {{-- Si la url es null, significa que son los puntos suspensivos "..." --}}
                                    @if (is_null($link['url']))
                                        <span class="pagination__link"
                                            style="cursor: default; color: var(--color-slate-400); user-select: none;">...</span>
                                    @elseif ($link['active'])
                                        <span
                                            class="pagination__link pagination__link--active">{{ $link['label'] }}</span>
                                    @else
                                        <a href="{{ $link['url'] }}" class="pagination__link">{{ $link['label'] }}</a>
                                    @endif
                                @endif
                            @endforeach
                        </div>

                        {{-- Botón Siguiente --}}
                        @if ($productos->hasMorePages())
                            <a href="{{ $productos->nextPageUrl() }}" class="pagination__btn">
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
        .data-table {
            background-color: var(--color-white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            width: 100%;
        }

        .data-table__wrapper {
            overflow-x: auto;
        }

        .data-table__main {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.95rem;
        }

        .data-table__head {
            background-color: var(--color-slate-50);
            border-bottom: 2px solid var(--color-slate-100);
        }

        .data-table__th {
            padding: 1.1rem 1.5rem;
            font-weight: 600;
            color: var(--color-slate-600);
            white-space: nowrap;
        }

        .data-table__th--center {
            text-align: center;
        }

        .data-table__th--right {
            text-align: right;
        }

        .data-table__row {
            border-bottom: 1px solid var(--color-slate-100);
            transition: var(--transition-smooth);
        }

        .data-table__row:not(.data-table__row--header):hover {
            background-color: rgba(148, 114, 184, 0.03);
        }

        .data-table__td {
            padding: 1.1rem 1.5rem;
            color: var(--color-slate-700);
            vertical-align: middle;
        }

        .data-table__td--bold {
            font-weight: 600;
            color: var(--color-slate-800);
        }

        .data-table__td--center {
            text-align: center;
        }

        .data-table__td--right {
            text-align: right;
            padding-right: 2.2rem !important;
        }

        .data-table__stock {
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-full);
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }

        .data-table__stock--in {
            background-color: var(--color-emerald-bg);
            color: var(--color-emerald-text);
        }

        .data-table__stock--low {
            background-color: #fef3c7;
            color: #b45309;
        }

        .data-table__actions {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }

        .data-table__btn {
            background-color: var(--color-slate-50);
            border: 1px solid var(--color-slate-100);
            border-radius: var(--radius-sm);
            color: var(--color-slate-500);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            transition: var(--transition-smooth);
            text-decoration: none;
        }

        .data-table__btn i {
            width: 1.1rem;
            height: 1.1rem;
        }

        .data-table__btn--edit:hover {
            background-color: var(--color-purple-bg);
            color: var(--color-purple-text);
            border-color: var(--color-primary-light);
        }

        .data-table__btn--delete:hover {
            background-color: #fee2e2;
            color: var(--color-terracotta);
            border-color: var(--color-terracotta);
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header__title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--color-slate-800);
            margin-bottom: 0.25rem;
        }

        .page-header__subtitle {
            font-size: 0.9rem;
            color: var(--color-slate-500);
        }

        .data-table__toolbar {
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1.5rem;
            background-color: var(--color-white);
            border-bottom: 1px solid var(--color-slate-100);
        }

        .data-table__search-box {
            position: relative;
            max-width: 300px;
            width: 100%;
        }

        .data-table__search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1rem;
            height: 1rem;
            color: var(--color-slate-400);
            pointer-events: none;
        }

        .data-table__search-input {
            font-family: inherit;
            font-size: 0.9rem;
            width: 100%;
            padding: 0.55rem 0.75rem 0.55rem 2.2rem;
            border: 1px solid var(--color-slate-100);
            border-radius: var(--radius-sm);
            background-color: var(--color-bg);
            color: var(--color-slate-800);
            outline: none;
            transition: var(--transition-smooth);
        }

        .data-table__search-input:focus {
            border-color: var(--color-primary);
            background-color: var(--color-white);
            box-shadow: 0 0 0 3px rgba(148, 114, 184, 0.1);
        }

        .data-table__th--sortable {
            cursor: pointer;
            user-select: none;
            transition: var(--transition-smooth);
            position: relative;
            padding-right: 2.2rem !important;
        }

        .data-table__th--sortable:hover {
            background-color: var(--color-slate-100);
            color: var(--color-slate-800);
        }

        .data-table__sort-icon {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 0.85rem;
            height: 0.85rem;
            color: var(--color-slate-400);
            display: inline-block;
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
        document.addEventListener("DOMContentLoaded", function() {
            // Variables de control pasadas desde PHP de forma segura
            const ordenActual = "{{ $orden ?? 'asc' }}";
            const columnaActual = "{{ $columna ?? 'id' }}";
            const busquedaActual = "{{ $buscar ?? '' }}";

            // 1. Escuchar los clics de ordenamiento en los encabezados th--sortable
            const cabeceras = document.querySelectorAll(".data-table__th--sortable");
            cabeceras.forEach(th => {
                th.addEventListener("click", function() {
                    const columnaSlug = this.getAttribute("data-slug");

                    // Si hacemos clic en la misma columna, invertimos el orden; si es nueva, entra como asc
                    let nuevoOrden = "asc";
                    if (columnaSlug === columnaActual) {
                        nuevoOrden = (ordenActual === "asc") ? "desc" : "asc";
                    }

                    // Construimos la URL conservando el filtro del buscador
                    let url = new URL(window.location.href);
                    url.searchParams.set("columna", columnaSlug);
                    url.searchParams.set("orden", nuevoOrden);
                    if (busquedaActual) {
                        url.searchParams.set("buscar", busquedaActual);
                    }

                    // Viajar a la URL ordenada por base de datos global
                    window.location.href = url.toString();
                });
            });
        });

        // Tu función nativa del modal para eliminar productos
        function abrirModalConfirmacion(boton) {
            const idProducto = boton.getAttribute('data-id');
            const nombreProducto = boton.getAttribute('data-nombre');
            const formId = `form-eliminar-${idProducto}`;

            if (typeof mostrarNotificacion === 'function') {
                mostrarNotificacion(
                    "¿Remover Producto?",
                    `¿Estás seguro de que deseas remover permanentemente "${nombreProducto}" del inventario? Esta acción no se puede deshacer.`,
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
                        <button type="button" onclick="document.getElementById('${formId}').submit()" class="modal__btn" style="background-color: var(--color-terracotta, #e06666); color: white; margin: 0;">
                            Eliminar
                        </button>
                    </div>
                `;
            }

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    </script>
@endsection
