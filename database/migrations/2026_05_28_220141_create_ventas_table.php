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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('cliente_nombre')->default('Cliente General');
            $table->string('metodo_pago');
            $table->decimal('tasa_cambio', 10, 2); // Almacena la tasa con la que se cobró
            $table->decimal('total_usd', 10, 2);   // El total en $
            $table->decimal('total_bs', 10, 2);    // El total calculado en Bs.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
