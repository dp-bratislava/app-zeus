<?php

namespace App\MediaLibrary\UrlGenerator;

use Spatie\MediaLibrary\Support\UrlGenerator\UrlGenerator;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;
use DateTimeInterface;


class CustomMediaUrlGenerator extends DefaultUrlGenerator
{
    /**
     * Get the URL for the media item routed through our authenticated media controller.
     */
    public function getUrl(): string
    {
        return route('media.private.show', [
            'media' => $this->media->id,
            'fileName' => $this->media->name,
        ]);
    }

    /**
     * Get the temporary/signed URL for the media item.
     */
    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        return URL::temporarySignedRoute(
            'media.private.show',
            $expiration,
            [
                'media' => $this->media->id,
                'fileName' => $this->media->name,
            ]
        );
    }
}