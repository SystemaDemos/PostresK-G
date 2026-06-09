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
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            // Relación con la cabecera (Si se borra la venta, se borra su detalle)
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            // Relación con el producto (Protegido, no permite borrar productos con historial)
            $table->foreignId('producto_id')->constrained('productos');

            // Datos puros de la transacción
            $table->integer('cantidad');
            $table->decimal('precio_unitario_usd', 10, 2); // Precio en $ congelado al momento de la venta

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
