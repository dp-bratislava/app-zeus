<?php

namespace App\Filament\Resources\TS\TicketItemGroupResource\Pages;

use App\Filament\Resources\TS\TicketItemGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditTicketItemGroup extends EditRecord
{
    protected static string $resource = TicketItemGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    public function getTitle(): string | Htmlable
    {
        return __('tickets/ticket-item-group.update_heading', ['title' => $this->record->title]);
    }  
}
