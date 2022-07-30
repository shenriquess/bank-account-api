<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\OperacaoController;

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

// Cria nova operação
Route::post('operacao', [OperacaoController::class, 'store']);

// Lista todas as operações de uma conta
Route::get('operacoes/{id_conta}', [OperacaoController::class, 'show']);

//cotacao de uma moeda - API do Banco Central
Route::get('cotacao/{moeda}', [OperacaoController::class, 'exibirCotacao']);

//Saldo de todas as moedas ou de uma moeda específica
Route::get('saldo/{id_conta}/{moeda?}', [OperacaoController::class, 'saldo']);