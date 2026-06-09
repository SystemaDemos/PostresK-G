<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\TasaCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    /**
     * TAREA 1: Muestra la terminal POS con los productos y la tasa real de la BD.
     */
    public function index()
    {
        // Traemos todos los productos ordenados de forma alfabética (evitamos fallos si no existe la columna 'activo')
        $productos = Producto::orderBy('nombre', 'asc')->get();

        // Buscamos el último valor insertado en el historial de tasas. Si está vacío, usa 45.00 por defecto
        $tasaCambio = TasaCambio::latest()->first()?->valor ?? 45.00;

        // Enviamos los productos y la tasa real a tu vista de KoraPOS
        return view('punto_de_venta', compact('productos', 'tasaCambio'));
    }

    /**
     * TAREA 2: Recibe el carrito de JavaScript y lo guarda en la Base de Datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'carrito' => 'required|array',
            'total_usd' => 'required|numeric',
            'total_bs' => 'required|numeric',
            'tasa_cambio' => 'required|numeric',
            'metodo_pago' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request) {

                // 1. Crear la cabecera en la tabla 'ventas'
                $venta = Venta::create([
                    'cliente_nombre' => $request->cliente_nombre ?? 'Cliente General',
                    'metodo_pago' => $request->metodo_pago,
                    'tasa_cambio' => $request->tasa_cambio,
                    'total_usd' => $request->total_usd,
                    'total_bs' => $request->total_bs,
                ]);

                // 2. Recorrer el carrito para guardar cada fila en 'detalle_ventas'
                foreach ($request->carrito as $item) {

                    $venta->detalles()->create([
                        'producto_id' => $item['id'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario_usd' => $item['precio'],
                    ]);

                    // 3. Descontar la cantidad vendida del stock real del producto
                    $producto = Producto::find($item['id']);
                    if ($producto) {
                        $producto->decrement('cantidad', $item['cantidad']);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => '¡Venta registrada y stock actualizado con éxito!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * TAREA 3: Muestra el historial unificado o el desglose detallado de ventas.
     */
    public function registroVentas(Request $request)
    {
        // Capturamos el parámetro 'vista'. Si no viene ninguno, por defecto será 'individuales'
        $vista = $request->query('vista', 'individuales');

        if ($vista === 'detalles') {
            
            $ventas = Venta::with('detalles.producto')->latest()->paginate(10)->withQueryString();
        } else {
            
            $ventas = Venta::latest()->paginate(10)->withQueryString();
        }

        return view('registro_de_ventas', compact('ventas', 'vista'));
    }

    /**
     * TAREA 4: Limpia por completo el historial de ventas y sus detalles de forma segura.
     */
    public function destruirHistorial()
    {
        try {
            DB::transaction(function () {
                // Detectamos el motor de base de datos actual (sqlite, mysql, etc.)
                $driver = DB::getDriverName();

                // 1. Desactivar restricciones de llaves foráneas según el motor
                if ($driver === 'sqlite') {
                    DB::statement('PRAGMA foreign_keys = OFF;');
                } else {
                    DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
                }

                // 2. Vaciar las tablas de forma segura
                if ($driver === 'sqlite') {
                    DB::table('detalle_ventas')->delete();
                    DB::table('ventas')->delete();

                    // Reiniciar los contadores auto-incrementales en SQLite
                    DB::table('sqlite_sequence')->where('name', 'detalle_ventas')->delete();
                    DB::table('sqlite_sequence')->where('name', 'ventas')->delete();
                } else {
                    DB::table('detalle_ventas')->truncate();
                    DB::table('ventas')->truncate();
                }

                // 3. Reactivar restricciones de llaves foráneas
                if ($driver === 'sqlite') {
                    DB::statement('PRAGMA foreign_keys = ON;');
                } else {
                    DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
                }
            });

            // Redirección tradicional inyectando las variables flash para app.blade.php
            return redirect()->back()->with([
                'title' => '¡Historial Vaciado!',
                'success' => 'Todas las operaciones y registros de venta han sido eliminados del sistema correctamente.'
            ]);
        } catch (\Exception $e) {
            // En caso de error, también redirige atrás con estética de alerta de tu layout
            return redirect()->back()->with([
                'title' => 'Error del Sistema',
                'error' => 'Hubo un problema al vaciar el historial: ' . $e->getMessage()
            ]);
        }
    }
}
