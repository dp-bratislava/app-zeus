<?php

use App\Http\Controllers\TestController;
use App\Models\Reports\Export;
use Dpb\WtfTmsBridge\Models\Photo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

Route::prefix('dev')->group(function () {
    Route::get('/tickets-test', [TestController::class, 'index']);
});

// exports
Route::middleware('auth')->group(function () {
    Route::get('/exports/{export}', function (Export $export) {
        // check permissions
        abort_unless(auth()->id() === $export->user_id, 403);

        // check file existence
        $fileName = $export->file_name;
        abort_unless(Storage::disk('report-exports')->exists($fileName), 404);

        // get download
        return Storage::disk('report-exports')->download($fileName);
    })->name('exports.download');
});

// custom photo gallery private route
Route::middleware(['web', 'auth'])->get('/photos/private/{photo}/{fileName?}', function (Photo $photo, ?string $fileName = null) {
    $path = $photo->absolutePath();

    abort_unless(is_file($path), 404);

    return response()->file($path);
})->where('fileName', '.*')->name('photos.private.show');
