<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\GatekeeperController;
use App\Http\Controllers\Api\PublicTicketController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventSettingController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::get('/public/tickets/{ticket_code}', [PublicTicketController::class, 'show']);
Route::get('/event-info', [EventSettingController::class, 'show']);
/*
|--------------------------------------------------------------------------
| Protected Admin & Gatekeeper Routes (Laravel Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard & Metrics
    Route::get('/admin/dashboard', [DashboardController::class, 'overview']);

    // CRUD Undangan
    Route::apiResource('invitations', InvitationController::class);

    // Gatekeeper Scanner
    Route::post('/gatekeeper/validate', [GatekeeperController::class, 'validateScan']);

    // event-info
    Route::put('/event-info', [EventSettingController::class, 'update']);
});

Route::options('{any}', function (Illuminate\Http\Request $request) {
    $origin = $request->header('Origin') ?: '*';
    return response('', 200)
        ->header('Access-Control-Allow-Origin', $origin)
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin')
        ->header('Access-Control-Allow-Credentials', 'true');
})->where('any', '.*');