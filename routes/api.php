<?php

use App\Http\Controllers\Api\VortexWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are protected by a shared secret (X-Vortex-Key header)
| for n8n integration.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Vortex Webhook Endpoints (n8n integration)
Route::prefix('vortex')->group(function () {
    // n8n POSTs results back to Laravel
    Route::post('/blog', [VortexWebhookController::class, 'receiveBlog']);
    Route::post('/mission', [VortexWebhookController::class, 'receiveMission']);
    Route::post('/report', [VortexWebhookController::class, 'receiveReportGrade']);

    // n8n GETs work from Laravel
    Route::get('/reports/queued', [VortexWebhookController::class, 'getQueuedReports']);
    Route::get('/arcs/completed', [VortexWebhookController::class, 'getCompletedArcs']);
});
