<?php

namespace App\Filament\Resources\Fleet\Vehicle\BrandResource\Forms;

use Filament\Forms;
use Filament\Forms\Form;

class BrandForm
{
    public static function make(Form $form): Form
    {
        return $form->schema(static::schema());
    }


    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->label(__('fleet/brand.form.fields.title'))
                ->required(),
        ];
    }
}
