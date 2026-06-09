<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class ProductoController extends Controller
{

    // Muestra el formulario de agregar y carga las categorías existentes
    public function create()
    {
        // Traemos todas las categorías para alimentar el <datalist> del formulario
        $categorias = Categoria::all();

        // Pasamos la variable a la vista
        return view('agregar_producto', compact('categorias'));
    }

    // Esto procesa el formulario, guarda en la BD y maneja errores
    public function store(Request $request)
    {
        // 1. Validar que los datos del kiosco sean correctos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'cantidad' => 'required|numeric|min:0',
            'precio_de_venta' => 'required|numeric|min:0'
        ]);

        try {

            // MAGIA: Si el texto de la categoría ya existe, lo busca. Si no, lo inserta automáticamente
            $categoria = Categoria::firstOrCreate([
                'nombre' => trim($request->input('categoria'))
            ]);

            // 2. Intentar guardar en la base de datos
            Producto::create([
                'nombre' => $request->input('nombre'),
                'cantidad' => $request->input('cantidad'),
                'precio_de_venta' => $request->input('precio_de_venta'),
                'categoria_id' => $categoria->id,
            ]);

            // Si todo sale bien, manda el éxito
            return redirect()->route('productos.create')->with('success', 'Producto agregado exitosamente');
        } catch (\Exception $e) {
            // Si la base de datos falla, atrapa el error, mantiene lo escrito y manda alerta
            return redirect()->route('productos.create')
                ->withInput()
                ->with('error', 'No se pudo conectar con la base de datos. Inténtalo de nuevo.');
        }
    }

    // Esto muestra la tabla con los productos reales de la BD, filtrados y ordenados globalmente
    public function index(Request $request)
    {
        // 1. Capturar los parámetros que vengan del buscador o de los clics en las cabeceras
        $buscar = $request->input('buscar');
        $columna = $request->input('columna', 'id'); // Columna por defecto
        $orden = $request->input('orden', 'asc');     // Orden por defecto

        // 2. Mapear qué columnas del HTML corresponden a las de la Base de Datos
        $columnasPermitidas = [
            'id'       => 'id',
            'nombre'   => 'nombre',
            'cantidad' => 'cantidad',
            'precio'   => 'precio_de_venta'
        ];

        $columnaReal = $columnasPermitidas[$columna] ?? 'id';

        // 3. Construir la consulta con filtros globales de Eloquent
        $query = Producto::with('categoria');

        // Si el usuario escribió algo en el input, filtramos globalmente por nombre o categoría
        if (!empty($buscar)) {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', '%' . $buscar . '%')
                    ->orWhereHas('categoria', function ($catQuery) use ($buscar) {
                        $catQuery->where('nombre', 'like', '%' . $buscar . '%');
                    });
            });
        }

        // Aplicamos el ordenamiento global antes de paginar
        $productos = $query->orderBy($columnaReal, $orden)->paginate(6);

        // Importante: Conservar los parámetros en los links de la paginación
        $productos->appends(['buscar' => $buscar, 'columna' => $columna, 'orden' => $orden]);

        return view('productos', compact('productos', 'buscar', 'columna', 'orden'));
    }

    // 1. Buscar el producto y pintar la vista de edición
    public function edit($id)
    {
        $producto = Producto::with('categoria')->findOrFail($id);
        $categorias = Categoria::all(); // <-- Traemos las categorías para el modal/vista de editar

        return view('editar_producto', compact('producto', 'categorias'));
    }

    // 2. Validar y guardar los nuevos datos en la BD
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:255', // <-- Validamos la categoría en la edición
            'cantidad' => 'required|integer|min:0',
            'precio_de_venta' => 'required|numeric|min:0'
        ]);

        try {
            $producto = Producto::findOrFail($id);

            // Procesamos la categoría igual que en el store (por si la cambia por una nueva)
            $categoria = Categoria::firstOrCreate([
                'nombre' => trim($request->input('categoria'))
            ]);

            $producto->update([
                'nombre' => $request->input('nombre'),
                'cantidad' => $request->input('cantidad'),
                'precio_de_venta' => $request->input('precio_de_venta'),
                'categoria_id' => $categoria->id, // <-- Actualizamos el ID de la relación
            ]);

            return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error interno: No se pudieron guardar los cambios.');
        }
    }

    // 3. Buscar el producto y borrarlo de la Base de Datos
    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producto->delete();

            return redirect()->route('productos.index')
                ->with('success', 'El producto fue removido del inventario permanentemente.')
                ->with('title', '¡Producto Eliminado!');
        } catch (\Exception $e) {
            return redirect()->route('productos.index')
                ->with('error', 'Error del sistema: No se pudo eliminar el producto.');
        }
    }

    // Esto carga la pantalla de la Terminal de Ventas (POS)
    public function terminalVentas()
    {
        // Traemos los productos ordenados de forma alfabética
        $productos = Producto::orderBy('nombre', 'asc')->get();

        // Retornamos tu vista 'punto_de_venta' pasándole los productos reales
        return view('punto_de_venta', compact('productos'));
    }
}
