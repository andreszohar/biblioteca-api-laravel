<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LibroResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'isbn' => $this->isbn,
            'anio_publicacion' => $this->anio_publicacion,
            'numero_paginas' => $this->numero_paginas,
            'descripcion' => $this->descripcion,
            'stock_disponible' => $this->stock_disponible,
            'esta_disponible' => $this->esta_disponible,
            'autores' => AutorResource::collection($this->whenLoaded('autores')),
            'creado_en' => $this->created_at?->toIso8601String(),
            'actualizado_en' => $this->updated_at?->toIso8601String(),
        ];
    }
}