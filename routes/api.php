<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// SMS Webhook Route
Route::post('/sms/webhook', [WebhookController::class, 'handleSmsWebhook']);