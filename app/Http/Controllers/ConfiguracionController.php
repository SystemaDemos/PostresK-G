<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Producto; // <-- Para modificar el stock masivamente

class ConfiguracionController extends Controller
{
    /**
     * Carga la vista principal de configuraciones
     */
    public function index()
    {
        return view('configuracion_del_sistema');
    }

    /**
     * TAREA 1: Generar Copia de Seguridad física (.sqlite o .sql)
     */
    public function respaldar()
    {
        try {
            $driver = DB::getDriverName();
            $fecha = date('Y-m-d_H-i-s');

            if ($driver === 'sqlite') {
                // Usamos la función global database_path() apuntando al archivo real
                $pathOriginal = database_path('database.sqlite');

                if (!file_exists($pathOriginal)) {
                    throw new \Exception("No se encontró el archivo físico de SQLite en la ruta especificada.");
                }

                // Forzar la descarga directa del archivo renombrado de manera segura
                return response()->download($pathOriginal, "KoraPOS_Backup_{$fecha}.sqlite");
            }

            if ($driver === 'mysql') {
                $filename = "KoraPOS_Backup_{$fecha}.sql";
                $pathDestino = storage_path("app/{$filename}");

                $comando = sprintf(
                    'mysqldump --user=%s --password=%s --host=%s %s > %s',
                    escapeshellarg(config('database.connections.mysql.username')),
                    escapeshellarg(config('database.connections.mysql.password')),
                    escapeshellarg(config('database.connections.mysql.host')),
                    escapeshellarg(config('database.connections.mysql.database')),
                    escapeshellarg($pathDestino)
                );

                exec($comando, $output, $returnVar);

                if ($returnVar !== 0) {
                    throw new \Exception("Error en el comando mysqldump. Revise privilegios.");
                }

                return response()->download($pathDestino)->deleteFileAfterSend(true);
            }

            throw new \Exception("Motor de base de datos no compatible.");
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'title' => 'Error de Respaldo',
                'error' => 'No se pudo procesar el archivo: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * TAREA EXTRAS: Reemplaza el archivo de base de datos actual con un backup subido.
     */
    public function restaurar(Request $request)
    {
        $request->validate([
            'archivo_backup' => 'required|file'
        ]);

        try {
            $driver = DB::getDriverName();
            $archivo = $request->file('archivo_backup');

            if ($driver === 'sqlite') {
                $pathDestino = database_path('database.sqlite');

                // Asegurar que cerramos las conexiones activas temporalmente antes de chocar el archivo
                DB::disconnect();

                // Copiar el archivo temporal sobre la base de datos de producción
                if (!copy($archivo->getRealPath(), $pathDestino)) {
                    throw new \Exception("Fallo al escribir en el disco el archivo de base de datos.");
                }

                return redirect()->back()->with([
                    'title' => '¡Respaldo Restaurado!',
                    'success' => 'El sistema cargó los datos del respaldo exitosamente. El inventario y registros fueron actualizados.'
                ]);
            }

            throw new \Exception("Esta función automática actualmente solo está configurada para motores SQLite locales.");
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'title' => 'Error de Restauración',
                'error' => 'No se pudo procesar el archivo cargado: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * TAREA 2: Optimización de Caché y Mantenimiento de Tablas Reales
     */
    public function optimizar()
    {
        try {
            // Comandos internos de la arquitectura de Laravel
            Artisan::call('cache:clear');
            Artisan::call('route:cache');
            Artisan::call('config:cache');
            Artisan::call('view:cache');

            // Mantenimiento físico usando los nombres exactos de tus archivos de migración
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                DB::statement('VACUUM;');
            } else if ($driver === 'mysql') {
                // Sincronizado con tus nombres reales de tablas
                DB::statement('OPTIMIZE TABLE productos, tasa_cambios, categorias, ventas, detalle_ventas;');
            }

            return redirect()->back()->with([
                'title' => '¡Sistema Optimizado!',
                'success' => 'La caché fue consolidada y los índices de las tablas se optimizaron con éxito.'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'title' => 'Error de Optimización',
                'error' => 'Error al ejecutar tareas de mantenimiento: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * TAREA 3: Modificación masiva del Stock a Cero sin romper llaves foráneas
     */
    public function resetStock()
    {
        try {
            // Modificamos el campo 'cantidad' basándonos en tu modelo de Producto
            Producto::query()->update(['cantidad' => 0]);

            return redirect()->back()->with([
                'title' => '¡Stock en Cero!',
                'success' => 'El inventario global de productos ha sido reiniciado correctamente.'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'title' => 'Error de Reinicio',
                'error' => 'No se pudo vaciar el stock de la tabla: ' . $e->getMessage()
            ]);
        }
    }
}
