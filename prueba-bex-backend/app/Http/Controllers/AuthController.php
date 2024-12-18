<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    /**
     * Intenta autenticar al usuario con el nombre de usuario y la contraseña proporcionados.
     * Si la autenticación es exitosa, devuelve un token de autenticación.
     * Si falla, devuelve un error de autenticación.
     *
     * @param \App\Http\Requests\LoginRequest $request El objeto de solicitud que contiene las credenciales del usuario.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con el token de autenticación si la autenticación es exitosa, o un error si no lo es.
     */
    public function login(LoginRequest $request)
    {
        try {
            if (!$token = auth('api')->attempt($request->only(['username', 'password']))) {
                return $this->error(__('users.nomatch'), 401);
            }
            return $this->responseToken($token);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
