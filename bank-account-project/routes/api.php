<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContaController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Lista todas as contas
Route::get('contas', [ContaController::class, 'index']);

// Lista conta individual
Route::get('conta/{id}', [ContaController::class, 'show']);

// Cria nova conta
Route::post('conta', [ContaController::class, 'store']);

