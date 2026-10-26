<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePrestamoRequest extends FormRequest
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
            'libro_id' => ['required', 'integer', 'exists:libros,id,deleted_at,NULL'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'libro_id.required' => 'Debes indicar el libro.',
            'libro_id.exists' => 'El libro indicado no existe.',
            'user_id.required' => 'Debes indicar el usuario.',
            'user_id.exists' => 'El usuario indicado no existe.',
        ];
    }
}