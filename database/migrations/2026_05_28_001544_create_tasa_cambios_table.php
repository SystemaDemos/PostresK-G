<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasas_cambio', function (Blueprint $table) {
            $table->id();
            // Usamos decimal de 8 dígitos en total y 2 decimales para los céntimos de Bolívar
            $table->decimal('valor', 8, 2);
            $table->timestamps(); // Esto nos dará created_at para saber cuándo se actualizó
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasa_cambio');
    }
};
