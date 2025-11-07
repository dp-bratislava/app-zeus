<?php

namespace App\Filament\Resources\Inspection\InspectionTemplateResource\Forms;

use Filament\Forms;
use Filament\Forms\Form;

class InspectionTemplateForm
{
    public static function make(Form $form): Form
    {
        return $form->schema(static::schema());
    }


    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('code'),
            Forms\Components\TextInput::make('title'),
            Forms\Components\TextInput::make('description'),
            // conditions - distance
            Forms\Components\TextInput::make('cnd_distance_treshold'),
            Forms\Components\TextInput::make('cnd_distance_1adv'),
            Forms\Components\TextInput::make('cnd_distance_2adv'),

            // conditions - time
            Forms\Components\TextInput::make('cnd_time_treshold'),
            Forms\Components\TextInput::make('cnd_time_1adv'),
            Forms\Components\TextInput::make('cnd_time_2adv'),

            Forms\Components\CheckboxList::make('groups')
                ->relationship('groups', 'title')
        ];
    }
}
