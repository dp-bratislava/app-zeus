<?php

use App\Http\Controllers\TestController;
use App\Models\Reports\Export;
use Illuminate\Support\Facades\Route;

Route::prefix('dev')->group(function () {
    Route::get('/tickets-test', [TestController::class, 'index']);
});

// exports
Route::middleware('auth')->group(function () {

    Route::get('/exports/{export}', function (Export $export) {

        abort_unless(auth()->user()->id === $export->user_id, 403);

        $path = storage_path("app/exports/{$export->file_name}.xlsx");

        abort_unless(file_exists($path), 404);

        return response()->download($path);
    })->name('exports.download');
});
