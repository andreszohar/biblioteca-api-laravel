<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrestamoResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'estado' => $this->estado,
            'fecha_prestamo' => $this->fecha_prestamo?->toDateString(),
            'fecha_devolucion_estimada' => $this->fecha_devolucion_estimada?->toDateString(),
            'fecha_devolucion_real' => $this->fecha_devolucion_real?->toDateString(),
            'libro' => new LibroResource($this->whenLoaded('libro')),
            'usuario' => new UserResource($this->whenLoaded('user')),
            'creado_en' => $this->created_at?->toIso8601String(),
        ];
    }
}