<?php

namespace App\Filament\Resources\DailyExpeditionResource\Pages;

use App\Filament\Resources\DailyExpeditionResource;
use App\Filament\Resources\DailyExpeditionResource\Forms\DailyExpeditionForm;
use App\Filament\Resources\DailyExpeditionResource\Forms\DailyExpeditionForm2;
use App\Services\DailyExpeditionRepository;
use Filament\Actions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
// use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class BulkCreateDailyExpedition2 extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static string $resource = DailyExpeditionResource::class;
    protected static string $view = 'filament.resources.daily-expedition.bulk-create';
    // public static function route(string $path): string
    // {
    //     return static::$resource::getSlug() . $path;
    // }

    public function getTitle(): string | Htmlable
    {
        return __('daily-expedition.create_heading');
    } 

    public function mount()
    {
        $this->form->fill([
            'vehicles' => DailyExpeditionForm2::defaultVehicles(),
            'date' => now()->toDateString(),
        ]);
    }

    public function form(Form $form): Form
    {
        return DailyExpeditionForm2::make($form)        
            ->statePath('data');;
    }

    public function create(): void
    {
        $data = $this->form->getState();

        app(DailyExpeditionRepository::class)->bulkCreate($data);

        // Notification::make()
        //     ->success()
        //     ->title('Created!')
        //     ->body('Daily expeditions created.')
        //     ->send();

        $this->redirect(DailyExpeditionResource::getUrl('index'));
    }
}
