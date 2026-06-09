@extends('layouts.app')

@section('title', 'Agregar Producto - PostresK&G')

@section('content')
    <div class="app__content">

        <header class="page-header">
            <h2 class="page-header__title">Agregar Nuevo Producto</h2>
            <p class="page-header__subtitle">Registra un nuevo producto o stock dentro del sistema POS.</p>
        </header>

        <div class="form-card">
            <form action="{{ route('productos.store') }}" method="POST" class="form-card__form">

                <div class="form-card__row">
                    @csrf <div class="form-card__group">
                        <label for="nombre" class="form-card__label">Nombre del producto</label>
                        <input type="text" id="nombre" name="nombre" class="form-card__input"
                            placeholder="Ej. pan con queso" required>
                    </div>

                    <div class="form-card__group">
                        <label for="categoria" class="form-card__label">Categoría</label>
                        <input type="text" id="categoria" name="categoria" list="categorias-list"
                            class="form-card__input" placeholder="Ej. Panadería, Cafetería" autocomplete="off"
                            onfocus="this.showPicker()" required>

                        <datalist id="categorias-list">
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->nombre }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div class="form-card__row">
                    <div class="form-card__group">
                        <label for="cantidad" class="form-card__label">Cantidad</label>
                        <input type="number" id="cantidad" name="cantidad" class="form-card__input" placeholder="0"
                            min="0" required>
                    </div>

                    <div class="form-card__group">
                        <label for="precio" class="form-card__label">Valor de Venta</label>
                        <input type="number" id="precio" name="precio_de_venta" class="form-card__input"
                            placeholder="0.000" step="0.001" min="0" required>
                    </div>
                </div>

                <div class="form-card__actions">
                    <button type="submit" class="form-card__btn form-card__btn--submit">
                        <i data-lucide="plus-circle" style="width: 1.1rem; height: 1.1rem;"></i>
                        <span>Guardar Producto</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    <style>
        /* ==========================================================================
                               Componente: Page Header (Título de la sección)
                               ========================================================================== */
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

        /* ==========================================================================
                               Componente: Form Card (Tarjeta contenedora del formulario)
                               ========================================================================== */
        .form-card {
            background-color: #ffd2fa;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 2rem;
            max-width: 650px;
            /* Ancho elegante y contenido para lectura fluida */
            width: 100%;
        }

        .form-card__group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            width: 100%;
        }

        .form-card__row {
            display: flex;
            gap: 1.5rem;
        }

        /* Ajuste responsive para pantallas móviles pequeñas */
        @media (max-width: 480px) {
            .form-card__row {
                flex-direction: column;
                gap: 0;
            }
        }

        .form-card__label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--color-slate-600);
        }

        .form-card__input {
            font-family: inherit;
            font-size: 1rem;
            padding: 0.75rem 1rem;
            border: 1px solid var(--color-slate-100);
            border-radius: var(--radius-md);
            color: var(--color-slate-800);
            background-color: var(--color-bg);
            transition: var(--transition-smooth);
            outline: none;
        }

        /* Efecto Focus usando tus variables moradas */
        .form-card__input:focus {
            border-color: var(--color-primary);
            background-color: var(--color-white);
            box-shadow: 0 0 0 3px rgba(148, 114, 184, 0.15);
        }

        .form-card__actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
        }

        .form-card__btn {
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition-smooth);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
        }

        /* Modificador: Botón Guardar (Morado Principal) */
        .form-card__btn--submit {
            background-color: var(--color-primary);
            color: var(--color-white);
        }

        .form-card__btn--submit:hover {
            background-color: var(--color-primary-light);
            box-shadow: var(--shadow-sm);
        }

        /* Modificador: Botón Cancelar */
        .form-card__btn--cancel {
            background-color: transparent;
            color: var(--color-slate-500);
            border: 1px solid var(--color-slate-100);
        }

        .form-card__btn--cancel:hover {
            background-color: var(--color-slate-50);
            color: var(--color-slate-700);
        }
    </style>
@endsection
