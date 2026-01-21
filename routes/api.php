<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('license')->name('license.')->group(function () {

    Route::post('/register-product/{id?}', [ApiController::class, 'register_product']);
    Route::post('/validate-product', [ApiController::class, 'validate_product']);
    Route::post('/get-active-domain', [ApiController::class, 'get_active_domain']);
    Route::post('/check-update', [ApiController::class, 'check_update']);
    Route::post('/reset-license', [ApiController::class, 'reset_license']);
});
