<?php

namespace App\Http\Controllers;

use App\Services\PhotoArchiveBuilder;
use Dpb\WtfTmsBridge\Models\Photo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GalleryDownloadController extends Controller
{
    /**
     * Download all photos of a gallery (photoable + collection) as a ZIP.
     *
     * Route: /photos/gallery/{photoableType}/{photoableId}/{collection?}
     * Protected by a signed URL so arbitrary model classes cannot be loaded.
     */
    public function __invoke(Request $request, string $photoableType, int $photoableId, string $collection = 'default', PhotoArchiveBuilder $archives): BinaryFileResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        // The photoableType is passed base64-encoded so it can travel safely in a URL.
        $photoableType = $this->resolvePhotoableType($photoableType);

        abort_unless(class_exists($photoableType) && is_subclass_of($photoableType, Model::class), 404);

        /** @var Model|null $photoable */
        $photoable = (new $photoableType)->newQuery()->find($photoableId);

        abort_if($photoable === null, 404);

        $photos = Photo::query()
            ->for($photoable, $collection)
            ->select(['id', 'disk', 'path', 'original_name', 'name'])
            ->orderBy('id')
            ->lazyById(100);

        $filenameBase = class_basename($photoable) ?: 'photos';

        return $archives->download($photos, $filenameBase);
    }

    protected function resolvePhotoableType(string $photoableType): string
    {
        $decoded = base64_decode(strtr($photoableType, '-_', '+/'), true);

        if ($decoded !== false && class_exists($decoded)) {
            return $decoded;
        }

        return $photoableType;
    }
}

