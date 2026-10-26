<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLibroRequest;
use App\Http\Requests\UpdateLibroRequest;
use App\Http\Resources\LibroResource;
use App\Models\Libro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class LibroController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $libros = Libro::with('autores')
            ->when($request->query('titulo'), fn ($q, $titulo) => $q->where('titulo', 'ILIKE', "%{$titulo}%"))
            ->when($request->query('anio'), fn ($q, $anio) => $q->porAnio((int) $anio))
            ->when($request->query('autor'), function ($q, $autor) {
                $q->whereHas('autores', function ($sub) use ($autor) {
                    $sub->where('nombre', 'ILIKE', "%{$autor}%")
                        ->orWhere('apellido', 'ILIKE', "%{$autor}%");
                });
            })
            ->when($request->boolean('disponibles'), fn ($q) => $q->disponibles())
            ->orderBy('titulo')
            ->paginate(min((int) $request->query('per_page', 15), 100));

        return LibroResource::collection($libros);
    }

    public function show(Libro $libro): LibroResource
    {
        return new LibroResource($libro->load('autores'));
    }

    public function store(StoreLibroRequest $request): JsonResponse
    {
        $libro = DB::transaction(function () use ($request) {
            $libro = Libro::create($request->safe()->except('autores'));
            $this->sincronizarAutores($libro, $request->validated('autores'));

            return $libro;
        });

        return (new LibroResource($libro->load('autores')))
            ->additional(['message' => 'Libro creado correctamente.'])
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateLibroRequest $request, Libro $libro): LibroResource
    {
        DB::transaction(function () use ($request, $libro) {
            $libro->update($request->safe()->except('autores'));
            $this->sincronizarAutores($libro, $request->validated('autores'));
        });

        return (new LibroResource($libro->load('autores')))
            ->additional(['message' => 'Libro actualizado correctamente.']);
    }

    public function destroy(Libro $libro): JsonResponse
    {
        // Soft delete: el registro queda con deleted_at y no se pierde el historial
        $libro->delete();

        return response()->json(['message' => 'Libro eliminado correctamente.']);
    }

    /**
     * El orden en que llegan los ids define el orden_autor (1, 2, 3...).
     */
    private function sincronizarAutores(Libro $libro, array $autorIds): void
    {
        $datos = [];
        foreach (array_values($autorIds) as $posicion => $autorId) {
            $datos[$autorId] = ['orden_autor' => $posicion + 1];
        }

        $libro->autores()->sync($datos);
    }
}