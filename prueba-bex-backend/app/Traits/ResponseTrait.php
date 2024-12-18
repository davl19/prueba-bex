<?php

namespace App\Traits;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

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

    /**
     * Formatea y devuelve una respuesta paginada basada en el tipo de colección proporcionada.
     * Esta función adapta la respuesta a diferentes tipos de paginadores o colecciones para 
     * proporcionar un formato de paginación consistente en las respuestas API.
     *
     * @param mixed $collection La colección de datos que se desea paginar. Puede ser:
     *                          - `\Illuminate\Pagination\LengthAwarePaginator`: Un paginador con información completa.
     *                          - `\Illuminate\Http\Resources\Json\AnonymousResourceCollection`: Una colección de recursos anónimos.
     *                          - `\Illuminate\Pagination\Paginator`: Un paginador simple.
     *                          - Una colección estándar como `\Illuminate\Support\Collection`.
     *
     * Comportamiento:
     * 1. Si `$collection` es una instancia de `LengthAwarePaginator` o `AnonymousResourceCollection`:
     *    - Devuelve los elementos paginados, el número total de páginas (`lastPage`) y el número total de registros (`total`).
     * 2. Si `$collection` es una instancia de `Paginator` (paginador simple):
     *    - Devuelve los elementos y establece `totalPages` en `1` y `totalRecords` con la cantidad de elementos actuales.
     * 3. Para colecciones estándar (`Collection`):
     *    - Devuelve todos los registros con `totalPages` en `1` y el conteo total de elementos.
     *
     * Ejemplo de respuesta:
     * ```json
     * {
     *     "success": true,
     *     "data": {
     *         "records": [...],
     *         "totalPages": 5,
     *         "totalRecords": 100
     *     }
     * }
     * ```
     *
     * @return \Illuminate\Http\JsonResponse Devuelve una respuesta en formato JSON con los datos paginados.
     */
    public function paginate($collection)
    {
        if (
            $collection instanceof \Illuminate\Pagination\LengthAwarePaginator
            || $collection instanceof \Illuminate\Http\Resources\Json\AnonymousResourceCollection
        ) {
            $response = [
                'records' => $collection->items(),
                'totalPages' => $collection->lastPage(),
                'totalRecords' => $collection->total(),
            ];
        } else if ($collection instanceof \Illuminate\Pagination\Paginator) {
            $response = [
                'records' => $collection->items(),
                'totalPages' => 1,
                'totalRecords' => count($collection->items()),
            ];
        } else {
            $response = [
                'records' => $collection,
                'totalPages' => 1,
                'totalRecords' => $collection->count(),
            ];
        }
        return $this->success($response);
    }

        /**
     * Maneja excepciones y devuelve respuestas de error personalizadas dependiendo del tipo de excepción.
     * Esta función permite capturar diferentes tipos de excepciones y retornar mensajes de error adaptados 
     * al contexto de la aplicación.
     *
     * @param \Throwable $th La excepción que se va a manejar.
     * @param bool $mp Indica si la excepción es un error de una API específica que contiene una respuesta 
     *                 personalizada. Por defecto es `false`.
     *
     * Comportamiento:
     * 1. Si `$mp` es `true`, se devuelve un mensaje de error extraído del contenido de la respuesta API.
     * 2. Si la excepción es una instancia de `TokenInvalidException`, lanza una nueva excepción con un 
     *    mensaje traducido correspondiente a un token inválido.
     * 3. Si la excepción es una instancia de `UnauthorizedException`, lanza una nueva excepción con el 
     *    código de error 401 y un mensaje traducido para denegación de acceso.
     * 4. Si no corresponde a los casos anteriores, se devuelve el mensaje genérico de la excepción.
     *
     * Ejemplo de uso:
     * ```php
     * try {
     *     // Código que podría lanzar una excepción
     * } catch (\Throwable $th) {
     *     return $this->errorException($th, true);
     * }
     * ```
     *
     * @return mixed Devuelve una respuesta personalizada según el tipo de excepción.
     *
     * @throws \TokenInvalidException Si la excepción corresponde a un token inválido.
     * @throws \UnauthorizedException Si la excepción corresponde a un error de autorización.
     */
    public function errorException($th, $mp = false)
    {
        if ($mp) {
            return $this->error($th->getApiResponse()->getContent()['message']);
        }
        if ($th instanceof TokenInvalidException) {
            throw new TokenInvalidException(__('invalidtoken'));
        }
        if ($th instanceof UnauthorizedException) {
            throw new UnauthorizedException(401, __('_401'));
        }
        return $this->error($th->getMessage());
    }

}
