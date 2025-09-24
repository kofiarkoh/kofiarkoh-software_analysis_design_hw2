<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaystackMockController;

Route::post('/transaction/initialize', [PaystackMockController::class, 'initializeTransaction']);
Route::post('/transferrecipient', [PaystackMockController::class, 'addTransferRecipient']);
Route::post('/transfer', [PaystackMockController::class, 'makeTransfer']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
