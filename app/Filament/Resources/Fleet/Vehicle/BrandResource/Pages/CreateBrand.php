<?php

namespace App\Filament\Resources\Fleet\Vehicle\BrandResource\Pages;

use App\Filament\Resources\Fleet\Vehicle\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateBrand extends CreateRecord
{
    protected static string $resource = BrandResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('fleet/vehicle-brand.create_heading');
    }
}
