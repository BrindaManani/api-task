<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register-product/{id?}', [ApiController::class, 'register_product']);
Route::post('/validate-product', [ApiController::class, 'validate_product']);