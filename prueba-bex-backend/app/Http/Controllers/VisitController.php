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
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit)
    {
        return $this->success($visit->delete(), "Visita eliminada correctamente");
    }
}
