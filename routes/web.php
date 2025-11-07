<?php

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::prefix('dev')->group(function () {
    Route::get('/tickets-test', [TestController::class, 'index']);
});