<?php

use App\Http\Controllers\SuccessfulEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/emails', [SuccessfulEmailController::class, 'store']);
Route::get('/emails/{id}', [SuccessfulEmailController::class, 'showOneRecord']);
Route::put('/emails/{id}', [SuccessfulEmailController::class, 'update']);
Route::get('/emails', [SuccessfulEmailController::class, 'index']);
Route::delete('/emails/{id}', [SuccessfulEmailController::class, 'destroy']);