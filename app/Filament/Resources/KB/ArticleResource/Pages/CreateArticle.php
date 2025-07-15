<?php

namespace App\Filament\Resources\KB\ArticleResource\Pages;

use App\Filament\Resources\KB\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;
}
