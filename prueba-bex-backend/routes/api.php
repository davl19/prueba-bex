<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth'
], function ($router) {
    //AUTH
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('api')->group(function () {
    // VISITS
    Route::apiResource('visits', VisitController::class);
});

//Route::get('/user', function (Request $request) {
//   return $request->user();
//})->middleware('auth:sanctum');
