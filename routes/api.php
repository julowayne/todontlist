<?php

use App\Http\Controllers\ApiTaskController;
use App\Http\Controllers\ApiTokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//Auth
Route::post('auth/register', [ApiTokenController::class, 'register']);
Route::post('auth/login', [ApiTokenController::class, 'login']);

// Account

Route::middleware('auth:sanctum')->group(function(){
    Route::post('auth/me', [ApiTokenController::class, 'me']);
    Route::post('auth/logout', [ApiTokenController::class, 'logout']);
    Route::get('/tasks', [ApiTaskController::class, 'index']);
    Route::get('/tasks/{id}', [ApiTaskController::class, 'show']);
    Route::post('/tasks', [ApiTaskController::class, 'store']);
});

Route::middleware('auth:sanctum')->post('auth/me', [ApiTokenController::class, 'me']);
Route::middleware('auth:sanctum')->post('auth/logout', [ApiTokenController::class, 'logout']);