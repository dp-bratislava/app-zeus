<?php

namespace App\Filament\Resources\TS\TicketItemResource\Pages;

use App\Filament\Resources\TS\TicketItemResource;
use App\Services\TicketItemRepository;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTicketItem extends CreateRecord
{
    protected static string $resource = TicketItemResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // dd($data);
        return app(TicketItemRepository::class)->create($data);
    }
}
