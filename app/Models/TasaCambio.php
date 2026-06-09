<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TasaCambio extends Model
{
    // Le indicamos el nombre exacto de tu tabla en SQLite
    protected $table = 'tasas_cambio';

    // Permitimos la asignación masiva del valor
    protected $fillable = ['valor'];
}
