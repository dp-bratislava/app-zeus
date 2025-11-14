<?php

namespace App\Filament\Resources\TS\TicketSourceResource\Pages;

use App\Filament\Resources\TS\TicketSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditTicketSource extends EditRecord
{
    protected static string $resource = TicketSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return __('tickets/ticket-source.update_heading', ['title' => $this->record->title]);
    }     
}
