<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    use HasFactory;

    // Campos permitidos para registrar en un Venta::create()
    protected $fillable = [
        'cliente_nombre',
        'metodo_pago',
        'tasa_cambio',
        'total_usd',
        'total_bs'
    ];

    /**
     * Relación Maestro-Detalle.
     * Una venta tiene muchos productos/detalles asociados.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }
}