<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaginatorRequest;
use App\Http\Requests\VisitStoreRequest;
use App\Http\Requests\VisitUpdateRequest;
use App\Http\Resources\VisitSingleResource;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }
    
    /**
     * @OA\Get(
     *     path="/api/visits",
     *     summary="Obtener lista de visitas",
     *     description="Obtener una lista de visitas paginada",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de elementos por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de visitas paginada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="records", type="array", @OA\Items(ref="#/components/schemas/Visit")),
     *             @OA\Property(property="totalPages", type="integer", example=1),
     *             @OA\Property(property="totalRecords", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function index(PaginatorRequest $request)
    {
        try {
            $query = Visit::viewWhere($request)->paginator($request);
            return $this->paginate($query);
        } catch (\Throwable $th) {
            return $this->errorException($th);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/visits",
     *     summary="Crear una nueva visita",
     *     description="Crear una nueva visita",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Visit")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Visita creada correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Visit")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function store(VisitStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $visit = Visit::create($request->only((new Visit())->getFillable()));
            DB::commit();
            return $this->success(new VisitSingleResource($visit), "Visita creada correctamente");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorException($th);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/visits/{id}",
     *     summary="Mostrar una visita específica",
     *     description="Obtener los detalles de una visita específica por su ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la visita",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la visita",
     *         @OA\JsonContent(ref="#/components/schemas/Visit")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Visita no encontrada"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function show(Visit $visit)
    {
        try {
            return $this->success(new VisitSingleResource($visit));
        } catch (\Throwable $th) {
            return $this->errorException($th);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/visits/{id}",
     *     summary="Actualizar una visita",
     *     description="Actualizar los detalles de una visita específica",
     *     security={{"bearerAuth":{}}}, 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la visita",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Visit")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Visita actualizada correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Visit")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Visita no encontrada"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function update(VisitUpdateRequest $request, Visit $visit)
    {
        try {
            $visit->update($request->all());
            return $this->success(new VisitSingleResource($visit), "Visita actualizada correctamente");
        } catch (\Throwable $th) {
            return $this->errorException($th);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/visits/{id}",
     *     summary="Eliminar una visita",
     *     description="Eliminar una visita específica por su ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la visita",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Visita eliminada correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Visita no encontrada"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function destroy(Visit $visit)
    {
        return $this->success($visit->delete(), "Visita eliminada correctamente");
    }
}
