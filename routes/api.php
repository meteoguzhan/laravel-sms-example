<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;

Route::prefix('v1')->group(function () {
    Route::get('/sms', [SmsController::class, 'index']);
    Route::post('/sms', [SmsController::class, 'store']);
    Route::get('/sms/{sms}', [SmsController::class, 'show']);
});