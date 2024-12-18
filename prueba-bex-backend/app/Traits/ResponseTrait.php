<?php

namespace App\Traits;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait ResponseTrait
{
    /**
     * Devuelve una respuesta exitosa con un token de autenticación y la información del usuario.
     *
     * @param string $token El token de autenticación que se va a devolver.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que contiene el token, su tipo, el tiempo de expiración y los detalles del usuario.
     */
    public function responseToken($token)
    {
        $user = User::findOrFail(auth()->user()->id);
        return $this->success([
            'token'                 => $token,
            'token_type'            => 'bearer',
            'expires_in'            => Auth::factory()->getTTL(),
            'user'                  => new UserResource($user),
        ]);
    }

    /**
     * Devuelve una respuesta de error con un mensaje personalizado, un código de estado HTTP y datos adicionales opcionales.
     *
     * @param string $message El mensaje de error para incluir en la respuesta. El valor predeterminado es una cadena vacía.
     * @param int $code El código de estado HTTP para el error. El valor predeterminado es 500 (Error Interno del Servidor).
     * @param mixed ...$arguments Argumentos adicionales opcionales para fusionar en la respuesta.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que contiene el estado de error y el mensaje.
     */
    public function error($message = '', $code = 500, ...$arguments)
    {
        return response()->json(
            array_merge([
                'status' => 'error',
                'message' => $message,
            ], ...$arguments),
            $code ?? 500
        );
    }

    /**
     * Devuelve una respuesta exitosa con los datos proporcionados, un mensaje de éxito y un código de estado HTTP.
     *
     * @param mixed $data Los datos a incluir en la respuesta. El valor predeterminado es null.
     * @param string $message El mensaje de éxito para incluir en la respuesta. El valor predeterminado es una cadena vacía.
     * @param int $code El código de estado HTTP para la respuesta. El valor predeterminado es 200 (OK).
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que contiene el estado de éxito, el mensaje y los datos.
     */
    public function success($data = null, $message = '', $code = 200)
    {
        return response()->json([
            'status' => 'ok',
            'message' => $message,
            'data' => $data,
        ], $code ?? 200);
    }

    /**
     * Devuelve una respuesta con un mensaje personalizado y un código de estado HTTP.
     *
     * @param string $message El mensaje a incluir en la respuesta. El valor predeterminado es una cadena vacía.
     * @param int $code El código de estado HTTP para la respuesta. El valor predeterminado es 200 (OK).
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON que contiene el estado de éxito y el mensaje.
     */
    public function message($message = '', $code = 200)
    {
        return response()->json([
            'status' => 'ok',
            'message' => $message,
        ], $code ?? 200);
    }

}
