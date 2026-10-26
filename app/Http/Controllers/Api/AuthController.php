<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (
            ! $user
            || ! Hash::check($request->validated('password'), $user->password)
            || $user->estado !== User::ESTADO_ACTIVO
        ) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'usuario' => (new UserResource($user))->resolve(),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}