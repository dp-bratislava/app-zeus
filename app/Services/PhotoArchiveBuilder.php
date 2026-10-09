<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class PhotoArchiveBuilder
{
    /**
     * Build a ZIP archive from the given photos and return a download response.
     *
     * @param  iterable<\Dpb\WtfTmsBridge\Models\Photo>  $photos
     */
    public function download(iterable $photos, string $filenameBase): BinaryFileResponse
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'photos-');

        abort_if($zipPath === false, 500, 'Cannot create temporary ZIP.');

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($zipPath);
            abort(500, 'Cannot create ZIP archive.');
        }

        try {
            foreach ($photos as $photo) {
                $this->addPhoto($zip, $photo);
            }
        } catch (\Throwable $e) {
            $zip->close();
            @unlink($zipPath);

            throw $e;
        }

        if (! $zip->close()) {
            @unlink($zipPath);
            abort(500, 'Could not finalize ZIP archive.');
        }

        $filename = sprintf(
            '%s-%s.zip',
            Str::slug($filenameBase) ?: 'photos',
            now()->format('Y-m-d')
        );

        return response()
            ->download($zipPath, $filename, [
                'Content-Type' => 'application/zip',
            ])
            ->deleteFileAfterSend(true);
    }

    protected function addPhoto(ZipArchive $zip, object $photo): void
    {
        // ZipArchive::addFile requires a local filesystem path.
        $path = Storage::disk($photo->disk)->path($photo->path);

        if (! is_file($path)) {
            return;
        }

        $extension = pathinfo($photo->path, PATHINFO_EXTENSION);

        $name = $photo->original_name
            ? pathinfo($photo->original_name, PATHINFO_FILENAME)
            : $photo->name;

        $archiveName = sprintf(
            '%s.%s',
            Str::slug($name) ?: 'photo',
            $extension
        );

        if (! $zip->addFile($path, $archiveName)) {
            throw new \RuntimeException("Could not add photo {$photo->getKey()} to ZIP.");
        }
    }
}
