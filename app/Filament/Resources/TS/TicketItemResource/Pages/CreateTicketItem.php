<?php

namespace App\Filament\Resources\TS\TicketItemResource\Pages;

use App\Filament\Resources\TS\TicketItemResource;
use App\Services\TicketItemRepository;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateTicketItem extends CreateRecord
{
    protected static string $resource = TicketItemResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('tickets/ticket.create_heading');
    } 

    protected function handleRecordCreation(array $data): Model
    {
        // dd($data);
        return app(TicketItemRepository::class)->create($data);
    }
}
