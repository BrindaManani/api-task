<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('license')->name('license.')->group(function () {

    Route::post('/register-product/{id?}', [ApiController::class, 'register_product']);
    Route::post('/validate-product', [ApiController::class, 'validate_product']);
    Route::post('/get-active-domain', [ApiController::class, 'get_active_domain']);
    Route::post('/check-update', [ApiController::class, 'check_update']);
    Route::post('/reset-license', [ApiController::class, 'reset_license']);
    Route::get('/buyers/{id?}', [ApiController::class, 'buyers']);
    Route::get('/api-requests', [ApiController::class, 'api_requests']);
    Route::get('/api-activities', [ApiController::class, 'api_activities']);
    Route::get('/download-update/sql/{vid} ', [ApiController::class, 'download_update_sql']);
    Route::get('/products/{id?}', [ApiController::class, 'products']);
    Route::get('/blocked-ips/{id?}', [ApiController::class, 'blocked_ips']);
    Route::get('/license-report', [ApiController::class, 'license_report']);
});
