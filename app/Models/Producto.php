<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    // Campos que Laravel tiene permitido guardar directamente del formulario
    protected $fillable = [
        'nombre',
        'cantidad',
        'precio_de_venta',
        'categoria_id'
    ];

    // Un producto pertenece a una categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
