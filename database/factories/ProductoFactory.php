<?php

namespace Database\Factories; // Cabecera obligatoria

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory // El nombre debe ser idéntico al archivo
{
    protected $model = Producto::class;

    public function definition(): array
    {
        // 1. Primero definimos la lista de opciones para tu negocio
        $categoriasListado = ['Panadería', 'Pastelería', 'Bebidas', 'Cafetería', 'Postres', 'Combos'];

        // 2. Elegimos una de esas categorías al azar usando Faker
        $categoriaAleatoria = $this->faker->randomElement($categoriasListado);

        // 3. La buscamos en la BD para no repetirla, o la creamos si no existe
        $categoria = Categoria::firstOrCreate(['nombre' => $categoriaAleatoria]);

        // 4. Retornamos el array con el ID ya resuelto
        return [
            // Genera un nombre de producto (ej: "Mantecada especial")
            'nombre' => ucfirst($this->faker->words(2, true)),

            // Genera stock aleatorio entre 1 y 60 unidades
            'cantidad' => $this->faker->numberBetween(1, 60),

            // Genera precios reales en dólares
            'precio_de_venta' => $this->faker->randomFloat(2, 0.5, 50),

            // Usamos directamente el ID de la categoría que conseguimos arriba
            'categoria_id' => $categoria->id,
        ];
    }
}
