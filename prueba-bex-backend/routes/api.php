<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth'
], function ($router) {
    //AUTH
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('api')->group(function () {
    
});

//Route::get('/user', function (Request $request) {
//   return $request->user();
//})->middleware('auth:sanctum');
