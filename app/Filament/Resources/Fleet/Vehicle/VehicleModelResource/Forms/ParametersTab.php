<?php

namespace App\Filament\Resources\Fleet\Vehicle\VehicleModelResource\Forms;

use App\Filament\Resources\Fleet\Vehicle\BrandResource\Forms\BrandPicker;
use App\Filament\Resources\Fleet\Vehicle\VehicleTypeResource\Forms\VehicleTypePicker;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Eav\Models\Attribute;
use Dpb\Package\Eav\Models\AttributeGroup;
use Filament\Forms;
use Filament\Forms\Form;

class ParametersTab
{
    public static function make(): array
    {
        $tabs = [];

        // Dynamic tabs
        foreach (AttributeGroup::get() as $attributeGroup) {
            $attributes = Attribute::byGroup($attributeGroup->code)->get();

            $attributeInputs = [];
            foreach ($attributes as $key => $attribute) {
                $attributeInputs[$key] = Forms\Components\TextInput::make($attribute->code)
                    ->label($attribute->title)
                    ->inlineLabel();
            }

            $tabs[$attributeGroup->code] = Forms\Components\Tabs\Tab::make($attributeGroup->title)
            ->schema($attributeInputs);
        }

        return [
            Forms\Components\Tabs::make('Tabs')
                ->tabs($tabs)
        ];
    }
}
