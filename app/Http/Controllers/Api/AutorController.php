<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AutorResource;
use App\Models\Autor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AutorController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return AutorResource::collection(
            Autor::withCount('libros')->orderBy('apellido')->paginate(15)
        );
    }

    public function destroy(Autor $autor): JsonResponse
    {
        if ($autor->libros()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el autor porque tiene libros asociados.',
            ], 409);
        }

        $autor->delete();

        return response()->json(['message' => 'Autor eliminado correctamente.']);
    }
}