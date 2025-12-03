<?php

namespace App\Filament\Resources\TS\TicketItemGroupResource\Forms;

use Filament\Forms;

class TicketItemGroupPicker
{
    public static function make(string $uri)
    {
        return Forms\Components\Select::make($uri)
            ->label(__('tickets/ticket-item-group.components.picker.label'))
            ->searchable()
            ->preload()
            ->createOptionForm(TicketItemGroupForm::schema())
            ->createOptionModalHeading(__('tickets/ticket-item-group.components.picker.create_heading'))
            ->editOptionForm(TicketItemGroupForm::schema())
            ->editOptionModalHeading(__('tickets/ticket-item-group.picker.update_heading'));
    }
}
