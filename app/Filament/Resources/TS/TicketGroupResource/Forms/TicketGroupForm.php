<?php

namespace App\Filament\Resources\TS\TicketGroupResource\Forms;

use Filament\Forms;
use Filament\Forms\Form;

class TicketGroupForm
{
    public static function make(Form $form): Form
    {
        return $form->schema(static::schema());
    }

    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('code')
                ->label(__('tickets/ticket-group.form.fields.code.label'))
                ->hint(__('tickets/ticket-group.form.fields.code.hint')),
            Forms\Components\TextInput::make('title')
                ->label(__('tickets/ticket-group.form.fields.title')),
        ];
    }
}
