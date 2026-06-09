<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\TasaCambioController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\EstadisticaController;

Route::get('/', function () {
    return view('index');
});

// =========================================================
// GESTIÓN DE INVENTARIO (PRODUCTOS & CATEGORÍAS)
// =========================================================

// 1. Catálogo / Tabla general de productos
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');

// 2. Formulario de registro e inserción en BD
Route::get('/agregar-producto', [ProductoController::class, 'create'])->name('productos.create');
Route::post('/guardar-producto', [ProductoController::class, 'store'])->name('productos.store');

// 3. Edición, actualización y eliminación
Route::get('/productos/{id}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');

// Carga la página unificada de registros (Historial o Detalles)
Route::get('/registro-ventas', [VentaController::class, 'registroVentas'])->name('ventas.registro');

Route::get('/estadisticas', [EstadisticaController::class, 'index'])->name('estadisticas.index');

// Elimina todo el historial de ventas (¡CUIDADO!)
Route::delete('/ventas/limpiar-historial', [VentaController::class, 'destruirHistorial'])->name('ventas.limpiar');


// =========================================================
// PANEL GENERAL - GESTIÓN DE TASAS DE CAMBIO (BCV)
// =========================================================

// Actualización de la tasa BCV desde el Panel General
Route::put('/tasa-cambio', [TasaCambioController::class, 'update'])->name('tasa.update');


// =========================================================
// TERMINAL DE VENTAS (POS) - GESTIONADO POR VENTACONTROLLER
// =========================================================

// Carga la pantalla del POS leyendo los productos y la tasa real de la BD
Route::get('/punto-de-venta', [VentaController::class, 'index'])->name('pos.index');

// Recibe el JSON del carrito (Fetch) y procesa el cobro/descuento de stock
Route::post('/punto-de-venta', [VentaController::class, 'store'])->name('pos.store');

// =========================================================
// CONFIGURACIÓN DEL SISTEMA - RESPALDO, RESTAURACIÓN, ETC.
// =========================================================

// Grupo de rutas para la gestión del sistema
Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
Route::post('/configuracion/respaldar', [ConfiguracionController::class, 'respaldar'])->name('configuracion.respaldar');
Route::post('/configuracion/restaurar', [ConfiguracionController::class, 'restaurar'])->name('configuracion.restaurar');
Route::post('/configuracion/optimizar', [ConfiguracionController::class, 'optimizar'])->name('configuracion.optimizar');
Route::put('/configuracion/reset-stock', [ConfiguracionController::class, 'resetStock'])->name('configuracion.reset-stock');
