<?php

namespace App\Filament\Resources\DailyExpeditionResource\Pages;

use App\Filament\Resources\DailyExpeditionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditDailyExpedition extends EditRecord
{
    protected static string $resource = DailyExpeditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    public function getTitle(): string | Htmlable
    {
        return __('daily-expedition.update_heading', ['title' => $this->record->id]);
    }  
}
