<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\PaginatorRequest;

trait PaginatorTrait
{
    /**
     * Define un scope personalizado para paginar los resultados de una consulta con opciones de ordenamiento dinámico y 
     * recursos específicos. Este método permite generar una respuesta paginada utilizando una clase de recurso definida 
     * en la arquitectura del proyecto, si está disponible.
     *
     * @param Builder $query La consulta de Eloquent que será modificada para la paginación y ordenamiento.
     * @param PaginatorRequest $request Una instancia de la solicitud personalizada que contiene los parámetros 
     *                                   necesarios para la paginación y el ordenamiento, como:
     *                                   - `sort`: El nombre de la columna por la cual se ordenará.
     *                                   - `order`: El tipo de ordenamiento (ascendente o descendente).
     *                                   - `per_page`: La cantidad de resultados por página.
     *                                   - `page`: La página actual para la paginación.
     *
     * Comportamiento:
     * 1. Si el campo de ordenamiento (`sort`) proporcionado está en la lista de atributos "fillable" del modelo, se utiliza
     *    para ordenar la consulta.
     * 2. Si el campo de ordenamiento está definido en la propiedad `$orderingColumns` del modelo, también se utiliza para 
     *    ordenar.
     * 3. Se verifica si existe una clase de recurso en los siguientes espacios de nombres:
     *    - `{Namespace\Http\Resources\{Modelo}\{Modelo}Resource`
     *    - `{Namespace\Http\Resources\{Modelo}Resource`
     *    Si la clase de recurso existe, se utiliza para transformar los resultados paginados.
     * 4. Si no se encuentra una clase de recurso válida, se devuelve directamente la colección paginada sin transformación.
     *
     * Ejemplo de uso:
     * ```php
     * $paginacion = Modelo::paginator($request);
     * return response()->json($paginacion);
     * ```
     *
     * @return mixed Colección paginada con o sin transformación basada en un recurso.
     */
    public function scopePaginator(Builder $query, PaginatorRequest $request)
    {
        $columns = collect($this->orderingColumns ?? []);
        if (in_array($request->sort,  $this->getFillable())) {
            $query->orderBy($request->sort, $request->order);
        } elseif ($sort = ($columns[$request->sort] ?? null)) {
            $query->orderBy($sort, $request->order);
        }

        $name = class_basename($this);
        $namespace = str((new \ReflectionClass($this))->getNamespaceName());
        $path = $namespace->explode('\\')->take(2)->push('Http\\Resources')->implode('\\');
        $p1 = "{$path}\\{$name}\\{$name}Resource";
        $p2 = "{$path}\\{$name}Resource";

        if (class_exists($p1)) {
            return $p1::collection($query->paginate($request->per_page, ['*'], 'page', $request->page));
        }

        if (class_exists($p2)) {
            return $p2::collection($query->paginate($request->per_page, ['*'], 'page', $request->page));
        }

        return $query->paginate($request->per_page, ['*'], 'page', $request->page);
    }
}
