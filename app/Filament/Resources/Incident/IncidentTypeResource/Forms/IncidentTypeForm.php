<?php

namespace App\Filament\Resources\Incident\IncidentTypeResource\Forms;

use Filament\Forms;
use Filament\Forms\Form;

class IncidentTypeForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->schema(static::schema())
            ->columns(6);
    }

    public static function schema(): array
    {
        return [
            // code
            Forms\Components\TextInput::make('code')
                ->label(__('incidents/incident-type.form.fields.code.label')),
            // title
            Forms\Components\TextInput::make('title')
                ->label(__('incidents/incident-type.form.fields.title.label')),

        ];
    }
}
