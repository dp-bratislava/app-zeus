<?php

use App\Http\Controllers\PhotoshootDownloadController;
use App\Models\Reports\Export;
use Dpb\WtfTmsBridge\Models\Photo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\GalleryDownloadController;

// Authenticated routes.
Route::middleware('auth')->group(function () {
    // Export downloads.
    Route::get('/exports/{export}', function (Export $export) {
        // Ensure the authenticated user owns the export.
        abort_unless(auth()->id() === $export->user_id, 403);

        // Ensure the export file exists.
        $fileName = $export->file_name;
        $disk = Storage::disk('report-exports');

        abort_unless($disk->exists($fileName), 404);

        return $disk->download($fileName);
    })->name('exports.download');

    // Private photo gallery.
    Route::get('/photos/private/{photo}/{fileName?}', function (
        Photo $photo,
        ?string $fileName = null
    ) {
        $path = $photo->absolutePath();

        abort_unless(is_file($path), 404);

        return response()->file($path);
    })
        ->where('fileName', '.*')
        ->name('photos.private.show');

    // Photoshoot downloads.
    Route::get(
        '/photoshoots/{photoshoot}/download',
        PhotoshootDownloadController::class
    )->name('photoshoots.download');

    // Gallery downloads (signed URL).
    Route::get(
        '/photos/gallery/{photoableType}/{photoableId}/{collection?}',
        GalleryDownloadController::class
    )->name('photos.gallery.download');
});