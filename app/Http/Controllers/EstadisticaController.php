<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EstadisticaController extends Controller
{
    public function index()
    {
        // ==========================================================================
        // 1. CONTROL DE TIEMPOS (Carbon)
        // ==========================================================================
        $hoy = Carbon::today();
        $ayer = Carbon::yesterday();
        $hace7Dias = Carbon::now()->subDays(6); // Incluye el día de hoy para los 7 días
        $inicioMes = Carbon::now()->startOfMonth();

        // ==========================================================================
        // 2. CÁLCULO DE MÉTRICAS PRINCIPALES (Tarjetas)
        // ==========================================================================

        // Tarjeta 1: Ingresos de hoy y cálculo de tendencia vs ayer
        $ingresosHoy = Venta::whereDate('created_at', $hoy)->sum('total_usd');
        $ingresosAyer = Venta::whereDate('created_at', $ayer)->sum('total_usd');

        $tendenciaAyer = 0;
        if ($ingresosAyer > 0) {
            $tendenciaAyer = (($ingresosHoy - $ingresosAyer) / $ingresosAyer) * 100;
        }

        // Tarjeta 2: Transacciones del día
        $transaccionesHoy = Venta::whereDate('created_at', $hoy)->count();

        // Tarjeta 3: Ticket promedio de la semana
        $ticketPromedioSemana = Venta::where('created_at', '>=', $hace7Dias)->avg('total_usd') ?? 0;

        // Tarjeta 4: Productos críticos (Bajo Stock - Menos de 5 unidades, por ejemplo)
        $productosCriticos = Producto::where('cantidad', '<=', 5)->count();

        $metaDiariaUSD = 50.00; // Define aquí tu meta diaria en dólares
        $porcentajeMeta = $ingresosHoy > 0 ? min(($ingresosHoy / $metaDiariaUSD) * 100, 100) : 0;

        // Calculamos cuántos productos se venden en promedio por cada transacción de hoy
        $totalProductosHoy = DB::table('detalle_ventas')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->whereDate('ventas.created_at', $hoy)
            ->sum('detalle_ventas.cantidad') ?? 0;

        $productosPorTicket = $transaccionesHoy > 0 ? ($totalProductosHoy / $transaccionesHoy) : 0;


        // ==========================================================================
        // 3. CONSULTAS PARA LOS GRÁFICOS (Chart.js)
        // ==========================================================================

        // Gráfico 1: Evolución de ventas de los últimos 7 días
        // Rellena con 0 los días donde no hubo ventas para que el gráfico no se rompa
        $ventasSemanalesRaw = Venta::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('SUM(total_usd) as total')
        )
            ->where('created_at', '>=', $hace7Dias->startOfDay())
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get()
            ->pluck('total', 'fecha');

        // Generamos la estructura exacta de los últimos 7 días para JavaScript
        $ventasSemanales = [];
        for ($i = 6; $i >= 0; $i--) {
            $fechaDia = Carbon::now()->subDays($i)->format('Y-m-d');
            $nombreDia = Carbon::now()->subDays($i)->isoFormat('dddd'); // Requiere tener local en español, o usas ->format('D')

            $ventasSemanales[] = [
                'dia' => ucfirst($nombreDia),
                'total' => $ventasSemanalesRaw->get($fechaDia, 0) // Si no hay ventas, pone 0
            ];
        }

        // Gráfico 2: Métodos de Pago más utilizados
        $metodosPago = Venta::select('metodo_pago', DB::raw('SUM(total_usd) as total'))
            ->whereNotNull('metodo_pago')
            ->where('metodo_pago', '!=', '')
            ->groupBy('metodo_pago')
            ->get();


        // ==========================================================================
        // 4. CONSULTAS PARA RÁNKING (Top Productos del Mes)
        // ==========================================================================
        // Buscamos en las relaciones de las tablas de detalles mediante agregación de DB
        $topProductos = DB::table('detalle_ventas')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->select('productos.nombre', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'))
            ->where('ventas.created_at', '>=', $inicioMes)
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_vendido', 'desc')
            ->take(3) // Extraemos los 3 mejores
            ->get();


        // Retornamos todos los datos unificados a tu vista limpia
        return view('estadisticas', compact(
            'ingresosHoy',
            'tendenciaAyer',
            'transaccionesHoy',
            'ticketPromedioSemana',
            'productosCriticos',
            'ventasSemanales',
            'metodosPago',
            'topProductos',
            'metaDiariaUSD',
            'porcentajeMeta',
            'productosPorTicket'
        ));
    }
}
