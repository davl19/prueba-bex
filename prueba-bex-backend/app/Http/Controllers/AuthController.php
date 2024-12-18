<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;

/**
 * @OA\Info(title="API Visitas con Autenticación", version="1.0")
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Por favor ingresa tu token JWT en el campo de autorización."
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Iniciar sesión",
     *     description="Intenta autenticar al usuario con el nombre de usuario y la contraseña proporcionados. Si la autenticación es exitosa, devuelve un token de autenticación.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="username", type="string", description="Nombre de usuario del usuario"),
     *             @OA\Property(property="password", type="string", description="Contraseña del usuario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Autenticación exitosa, retorna el token JWT",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token", type="string", description="Token JWT de autenticación")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales incorrectas, no autorizado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Credenciales incorrectas")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", description="Detalles del error")
     *         )
     *     )
     * )
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
