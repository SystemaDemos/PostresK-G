<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Producto;
use App\Models\Categoria;

class MigracionProductosSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('sql/backup_cafebbd.sql');

        if (!file_exists($path)) {
            $this->command->error("No se encontró el archivo SQL en: $path");
            return;
        }

        $sqlContent = file_get_contents($path);

        // 1. Extraer los bloques de INSERT de categorías viejas y poblar primero las categorías
        preg_match_all("/INSERT INTO `categorias` VALUES\s+([^;]+);/i", $sqlContent, $matchesCategorias);

        $mapCategoriasViejas = [
            1 => 'Chucherias',
            2 => 'Galletas',
            3 => 'Detallados',
            4 => 'Bebidas',
            5 => 'Comidas',
            6 => 'Panaderias',
            7 => 'Higiene',
            8 => 'Caramelos',
            9 => 'Empanadas',
            10 => 'Libreria'
        ];

        $this->command->info("Creando o verificando categorías...");
        foreach ($mapCategoriasViejas as $idViejo => $nombreCat) {
            $categoriaNueva = Categoria::firstOrCreate(['nombre' => $nombreCat]);
            // Guardamos la relación del ID viejo de la BD anterior con el ID real que asigne Laravel
            $mapCategoriasIds[$idViejo] = $categoriaNueva->id;
        }

        // 2. Extraer los bloques de INSERT de la tabla `productos` vieja
        $this->command->info("Migrando productos...");
        preg_match_all("/INSERT INTO `productos` VALUES\s+([^;]+);/i", $sqlContent, $matchesProductos);

        $contador = 0;

        foreach ($matchesProductos[1] as $bloqueValores) {
            // Dividir el bloque en registros individuales manejando los paréntesis de SQL
            preg_match_all("/\(([^)]+)\)/", $bloqueValores, $registros);

            foreach ($registros[1] as $registro) {
                // Separar los valores por comas respetando las comillas
                $valores = str_getcsv($registro, ",", "'");

                if (count($valores) >= 6) {
                    // MAPEADO CORREGIDO CON TUS DATOS REALES:
                    $nombre = trim($valores[1]);
                    $categoriaViejaId = (int)$valores[2];
                    $cantidad = (int)$valores[3];

                    // INDEX 5: Ahora sí toma el valor de CostoventaP (ej: 0.340)
                    $precioDeVenta = (float)$valores[5];

                    // INDEX 9: Estado o Activo
                    $activo = isset($valores[9]) && (int)$valores[9] === 0 ? false : true;

                    // Buscar el ID equivalente de la categoría en tu BD actual
                    $categoriaActualId = $mapCategoriasIds[$categoriaViejaId] ?? null;

                    // Insertar o actualizar en la nueva estructura del POS
                    Producto::create([
                        'nombre' => $nombre,
                        'cantidad' => $cantidad,
                        'precio_de_venta' => $precioDeVenta,
                        'categoria_id' => $categoriaActualId,
                        'activo' => $activo,
                    ]);

                    $contador++;
                }
            }
        }

        $this->command->info("¡Éxito total! Se migraron un total de {$contador} productos correctamente.");
    }
}
