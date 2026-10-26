<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AutorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'nombre_completo' => $this->nombre_completo,
            'fecha_nacimiento' => $this->fecha_nacimiento?->toDateString(),
            'nacionalidad' => $this->nacionalidad,
            'biografia' => $this->biografia,
            'orden_autor' => $this->whenPivotLoaded(
                'autor_libro',
                fn () => $this->pivot->orden_autor
            ),
        ];
    }
}