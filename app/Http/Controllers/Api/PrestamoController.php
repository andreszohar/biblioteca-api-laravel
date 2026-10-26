<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrestamoRequest;
use App\Http\Resources\PrestamoResource;
use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\User;
use App\Services\PrestamoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PrestamoController extends Controller
{
    public function __construct(private readonly PrestamoService $prestamos)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $prestamos = Prestamo::with(['libro.autores', 'user'])
            ->when($request->query('estado'), fn ($q, $estado) => $q->where('estado', $estado))
            ->when($request->query('user_id'), fn ($q, $userId) => $q->where('user_id', $userId))
            ->latest('id')
            ->paginate((int) $request->query('per_page', 15));

        return PrestamoResource::collection($prestamos);
    }

    public function store(StorePrestamoRequest $request): JsonResponse
    {
        $prestamo = $this->prestamos->crear(
            User::findOrFail($request->validated('user_id')),
            Libro::findOrFail($request->validated('libro_id')),
        );

        return (new PrestamoResource($prestamo->load(['libro.autores', 'user'])))
            ->additional(['message' => 'Préstamo creado correctamente.'])
            ->response()
            ->setStatusCode(201);
    }

    public function devolver(Prestamo $prestamo): JsonResponse
    {
        $prestamo = $this->prestamos->devolver($prestamo);

        return (new PrestamoResource($prestamo->load(['libro.autores', 'user'])))
            ->additional(['message' => 'Préstamo devuelto correctamente.'])
            ->response();
    }
}