<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Dpb\WtfTmsBridge\Models\Photo;
use Dpb\WtfTmsBridge\Services\ImageManipulationService;

class GenerateMissingPhotoPreviews extends Command
{
    protected $signature = 'app:generate-missing-photo-previews';

    protected $description = 'If a photo was made before preview midsize images were introduced, this command will generate them';

    public function handle(): int
    {
        $imageService = app(ImageManipulationService::class);

        $photosToConsider = Photo::query()
            ->where(function ($query) {
                $query->whereNull('preview_path')
                    ->orWhereNull('thumbnail_path');
            })
            ->get();

        $this->info("Found {$photosToConsider->count()} photo(s) missing previews/thumbnails.");

        $bar = $this->output->createProgressBar($photosToConsider->count());
        $bar->start();

        $generated = 0;
        $failed = 0;

        foreach ($photosToConsider as $photo) {
            try {
                $disk = Storage::disk($photo->disk);
                $mainPath = $photo->path;

                if (! $photo->preview_path) {
                    if ($disk->exists($mainPath)) {
                        $photo->preview_path = $imageService->generatePreview(
                            $disk->path($mainPath),
                            $photo->disk,
                        );
                    }
                }

                if (! $photo->thumbnail_path) {
                    if ($disk->exists($mainPath)) {
                        $photo->thumbnail_path = $imageService->generateThumbnail(
                            $disk->path($mainPath),
                            $photo->disk,
                        );
                    }
                }

                $photo->save();
                $generated++;
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->error("Photo #{$photo->id} failed: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done. Generated: {$generated}, Failed: {$failed}.");

        return Command::SUCCESS;
    }
}