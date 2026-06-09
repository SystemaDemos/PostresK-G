<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TasaCambio;

class TasaCambioController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'tasa_bcv' => 'required|numeric|min:0.01'
        ]);

        // Insertamos un nuevo registro en el historial
        TasaCambio::create([
            'valor' => $request->input('tasa_bcv')
        ]);

        return redirect()->back()->with('success', 'Tasa del dólar actualizada correctamente.');
    }

    public function index()
    {
        // Obtiene el último valor insertado en el historial. Si no hay ninguno, por defecto muestra 0.00
        $tasaActiva = TasaCambio::latest()->first()?->valor ?? 0.00;

        // Pasamos la variable a la vista del panel
        return view('panel_general', compact('tasaActiva'));
    }
}
