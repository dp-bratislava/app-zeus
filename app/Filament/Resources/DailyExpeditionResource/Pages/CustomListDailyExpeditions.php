<?php

namespace App\Filament\Resources\DailyExpeditionResource\Pages;

use App\Filament\Resources\DailyExpeditionResource;
use App\Filament\Resources\DailyExpeditionResource\Forms\DailyExpeditionForm;
use App\Models\DailyExpedition;
use App\Services\DailyExpeditionRepository;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class CustomListDailyExpeditions extends Page
{

    public ?array $data = [];

    protected static string $resource = DailyExpeditionResource::class;
    protected static string $view = 'filament.resources.daily-expedition.custom-index';

    public $dailyExpeditions;
    public ?string $filterDate = null;

    // public getTitle

    public function mount(): void
    {
        $this->filterDate = now()->toDateString(); // default today
        $this->loadFilteredData();
    }

    public function updatedFilterDate()
    {
        $this->loadFilteredData();
    }

    public function loadFilteredData()
    {
        $this->dailyExpeditions = app(DailyExpedition::class)
            ->with(['vehicle.model', 'vehicle.codes'])
            ->where('date', '=', $this->filterDate)
            ->get()
            ->groupBy('vehicle.model.title')
            ->toArray();
    }
}
