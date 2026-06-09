<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        // Le indicamos al factory que cree exactamente 50 productos en la BD
        Producto::factory()->count(50)->create();
    }
}
