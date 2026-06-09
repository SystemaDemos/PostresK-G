<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleVenta extends Model
{
    use HasFactory;

    // Le indicamos explícitamente el nombre de la tabla de la migración
    protected $table = 'detalle_ventas';

    // Campos permitidos para guardar desde el bucle del carrito
    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario_usd'
    ];

    /**
     * Cada fila de detalle pertenece a una única venta madre.
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    /**
     * Cada fila de detalle hace referencia a un producto del inventario.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
