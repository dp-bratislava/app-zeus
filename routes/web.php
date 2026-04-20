<?php

use App\Http\Controllers\TestController;
use App\Models\Reports\Export;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::prefix('dev')->group(function () {
    Route::get('/tickets-test', [TestController::class, 'index']);
});

// exports
Route::middleware('auth')->group(function () {
    Route::get('/exports/{export}', function (Export $export) {
        // check permissions
        abort_unless(auth()->id() === $export->user_id, 403);

        // check file existence
        $fileName = $export->file_name . '.xlsx';
        abort_unless(Storage::disk('report-exports')->exists($fileName), 404);

        // get download 
        return Storage::disk('report-exports')->download($fileName);
    })->name('exports.download');
});
