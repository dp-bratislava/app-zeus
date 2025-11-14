<?php

namespace App\Filament\Resources\TS\TicketGroupResource\Pages;

use App\Filament\Resources\TS\TicketGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditTicketGroup extends EditRecord
{
    protected static string $resource = TicketGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    public function getTitle(): string | Htmlable
    {
        return __('tickets/ticket-group.update_heading', ['title' => $this->record->title]);
    }  
}
