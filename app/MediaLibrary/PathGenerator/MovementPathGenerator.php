<?php

namespace App\MediaLibrary\PathGenerator;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class MovementPathGenerator implements PathGenerator
{
    /**
     * All assets for movement photos go under agregaty/ (relative to MEDIA_PREFIX)
     */
    public function getPath(Media $media): string
    {
        return 'agregaty/';
    }

    public function getPathForConversions(Media $media): string
    {
        return 'agregaty/' . 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return 'agregaty/' . 'responsive-images/';
    }
}
