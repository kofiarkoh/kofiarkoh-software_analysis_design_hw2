<?php

use App\Http\Controllers\Api\PaystackWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/paystack-response', PaystackWebhookController::class);
