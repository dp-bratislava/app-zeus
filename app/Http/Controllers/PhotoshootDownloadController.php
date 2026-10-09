<?php
namespace App\Http\Controllers;

use App\Services\PhotoArchiveBuilder;
use Dpb\WtfTmsBridge\Models\Photoshoot;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
class PhotoshootDownloadController extends Controller
{
    public function __invoke(Photoshoot $photoshoot, PhotoArchiveBuilder $archives): BinaryFileResponse
    {
        $photos = $photoshoot->photos()
            ->select(['id', 'disk', 'path', 'original_name', 'name'])
            ->orderBy('id')
            ->lazyById(100);

        return $archives->download($photos, $photoshoot->title ?: 'photos');
    }
}

