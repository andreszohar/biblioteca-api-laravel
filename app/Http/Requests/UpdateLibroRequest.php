<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLibroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'isbn' => [
                'required',
                'string',
                'max:17',
                Rule::unique('libros', 'isbn')->ignore($this->route('libro')),
            ],
            'anio_publicacion' => ['required', 'integer', 'min:1000', 'max:' . now()->year],
            'numero_paginas' => ['required', 'integer', 'min:1', 'max:10000'],
            'descripcion' => ['nullable', 'string'],
            'stock_disponible' => ['required', 'integer', 'min:0'],
            'autores' => ['required', 'array', 'min:1'],
            'autores.*' => ['integer', 'distinct', 'exists:autores,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'isbn.required' => 'El ISBN es obligatorio.',
            'isbn.unique' => 'Ya existe otro libro con ese ISBN.',
            'anio_publicacion.max' => 'El año de publicación no puede ser futuro.',
            'stock_disponible.min' => 'El stock no puede ser negativo.',
            'autores.required' => 'Debes indicar al menos un autor.',
            'autores.*.exists' => 'Alguno de los autores indicados no existe.',
        ];
    }
}