<?php

namespace App\Filament\Resources\DailyExpeditionResource\Pages;

use App\Filament\Resources\DailyExpeditionResource;
use App\Services\DailyExpeditionRepository;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDailyExpedition extends CreateRecord
{
    protected static string $resource = DailyExpeditionResource::class;

    // protected function handleRecordCreation(array $data): Model
    // {
        // dd($data);

        // return $this->incidentService->createIncident($data);
        // return app(DailyExpeditionRepository::class)->bulkCreate($data);
    // }    
}
