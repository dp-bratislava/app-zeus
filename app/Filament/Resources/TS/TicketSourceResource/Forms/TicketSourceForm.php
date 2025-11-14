<?php

namespace App\Filament\Resources\TS\TicketSourceResource\Forms;

use Filament\Forms;
use Filament\Forms\Form;

class TicketSourceForm
{
    public static function make(Form $form): Form
    {
        return $form->schema(static::schema());
    }

    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('code')
                ->label(__('tickets/ticket-source.form.fields.code.label'))
                ->hint(__('tickets/ticket-source.form.fields.code.hint')),
            Forms\Components\TextInput::make('title')
                ->label(__('tickets/ticket-source.form.fields.title')),
        ];
    }
}
