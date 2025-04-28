<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Http\Requests\EstudianteRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class EstudianteController extends Controller
{
    /**
     * Muestra una lista de todos los estudiantes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $busqueda = $request->get('busqueda');
        
        $estudiantes = Estudiante::when($busqueda, function($query) use ($busqueda) {
            // Recorta y convierte a minúsculas el término de búsqueda para una mejor comparación
            $term = trim(strtolower($busqueda));
            
            return $query->where(function($q) use ($term) {
                // Usa whereRaw con la función LOWER para búsqueda sin distinción de mayúsculas/minúsculas
                // y solo usa comodín al final para un mejor uso del índice
                $q->whereRaw('LOWER(nombre) LIKE ?', [$term.'%'])
                  ->orWhereRaw('LOWER(edad) LIKE ?', [$term.'%']);
            });
        })
        ->orderBy('created_at', 'desc') // Ordena por fecha de creación, mostrando los más recientes primero
        ->paginate(10);
        
        return view('estudiantes.index', compact('estudiantes', 'busqueda'));
    }

    /**
     * Muestra el formulario para crear un nuevo estudiante.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('estudiantes.create');
    }

    /**
     * Almacena un nuevo estudiante en la base de datos.
     *
     * @param  \App\Http\Requests\EstudianteRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EstudianteRequest $request)
    {
        Estudiante::create($request->validated());
        
        return redirect()->route('estudiantes.index')
            ->with('success', 'Estudiante creado exitosamente.');
    }

    /**
     * Muestra los detalles de un estudiante específico.
     *
     * @param  \App\Models\Estudiante  $estudiante
     * @return \Illuminate\View\View
     */
    public function show(Estudiante $estudiante)
    {
        return view('estudiantes.show', compact('estudiante'));
    }

    /**
     * Muestra el formulario para editar un estudiante específico.
     *
     * @param  \App\Models\Estudiante  $estudiante
     * @return \Illuminate\View\View
     */
    public function edit(Estudiante $estudiante)
    {
        return view('estudiantes.edit', compact('estudiante'));
    }

    /**
     * Actualiza un estudiante específico en la base de datos.
     *
     * @param  \App\Http\Requests\EstudianteRequest  $request
     * @param  \App\Models\Estudiante  $estudiante
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EstudianteRequest $request, Estudiante $estudiante)
    {
        // Los datos ya están validados por el Request
        $estudiante->update($request->validated());
        
        return redirect()->route('estudiantes.index')
            ->with('success', 'Estudiante actualizado exitosamente.');
    }

    /**
     * Elimina un estudiante específico de la base de datos.
     *
     * @param  \App\Models\Estudiante  $estudiante
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Estudiante $estudiante)
    {
        $estudiante->delete();
        
        return redirect()->route('estudiantes.index')
            ->with('success', 'Estudiante eliminado exitosamente.');
    }

    /**
     * Exporta la lista de estudiantes a PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPDF()
    {
        $estudiantes = Estudiante::all();
        $pdf = PDF::loadView('estudiantes.pdf', compact('estudiantes'));
        
        return $pdf->download('estudiantes.pdf');
    }

    /**
     * Exporta la lista de estudiantes a Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        $estudiantes = Estudiante::all();

        $filename = 'estudiantes.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($estudiantes) {
            $file = fopen('php://output', 'w');

            // Añadir BOM para solucionar problemas de codificación en Excel con UTF-8.
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['ID', 'Nombre', 'Edad']);

            foreach ($estudiantes as $estudiante) {
                fputcsv($file, [
                    $estudiante->id,
                    $estudiante->nombre,
                    $estudiante->edad
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
