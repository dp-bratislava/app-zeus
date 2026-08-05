<?php

namespace App\Filament\Pages;

use Dpb\MasterPermissionGuard\Concerns\HasPageGuard;
use Dpb\Package\Assets\Models\AssetMovement;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Tasks\Models\TaskItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Dpb\Package\TaskMS\UI\Filament\Components\VehiclePicker;
use Dpb\Package\TaskMS\UI\Filament\Components\FleetVehiclePicker;
use Dpb\Package\TaskMS\Services\SelectOptions\VehiclePickerOptions;

class AssetsPhotoPage extends Page implements HasForms
{
    use HasPageGuard;
    use InteractsWithForms;

    protected string $view = 'filament.pages.assets-photo-page';

    protected static ?string $navigationLabel = 'Assets Photo';

    protected static ?string $title = 'Assets Photo';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Select Asset Movement')
                    ->schema([

                FleetVehiclePicker::make('vehicle_id')
                    ->label(__('wtf-tms-bridge-ui::tasks/task.form.fields.subject'))
                    ->columnSpan(1)
                    ->options(fn (VehiclePickerOptions $options) => $options->forTaskAssignment())
                    ->getOptionLabelFromRecordUsing(null)
                    ->getSearchResultsUsing(null)
                    ->preload()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('task_item_id', null);
                        $set('asset_movement_id', null);
                    })
                    ->required(),

                Select::make('task_item_id')
                    ->label('Task Item')
                    ->options(function (Get $get) {
                        $vehicleId = $get('vehicle_id');

                        if (blank($vehicleId)) {
                            return [];
                        }

                        return TaskItem::query()
                            ->whereHas('assetSlots', function (Builder $query) use ($vehicleId) {
                                $query->where('vehicle_id', $vehicleId);
                            })
                            ->get()
                            ->mapWithKeys(fn (TaskItem $taskItem) => [
                                $taskItem->id => (string) ($taskItem->title ?? "Task #{$taskItem->id}"),
                            ])
                            ->all();
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->disabled(fn (Get $get) => blank($get('vehicle_id')))
                    ->afterStateUpdated(fn (Set $set) => $set('asset_movement_id', null))
                    ->required(),

                Select::make('asset_movement_id')
                    ->label('Asset Movement')
                    ->options(function (Get $get) {
                        $taskItemId = $get('task_item_id');

                        if (blank($taskItemId)) {
                            return [];
                        }

                        $taskItem = TaskItem::find($taskItemId);

                        if (! $taskItem) {
                            return [];
                        }

                        return $taskItem->assetMovements()
                            ->get()
                            ->mapWithKeys(function (AssetMovement $movement) {
                                $label = '#' . $movement->id . ' ' . ($movement->asset_serial_number ?? '');

                                return [$movement->id => trim($label)];
                            })
                            ->all();
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->disabled(fn (Get $get) => blank($get('task_item_id')))
                    ->required(),
                    ]),

                Section::make('Upload Photos')
                    ->visible(fn (Get $get) => filled($get('asset_movement_id')))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('photos')
                            ->label('Photos')
                            ->collection('movement-photos')
                            ->multiple()
                            ->image()
                            ->imagePreviewHeight('420')
                            ->panelLayout('grid')
                            ->reorderable()
                            ->downloadable()
                            ->openable()
                            ->extraInputAttributes(['accept' => 'image/*', 'capture' => 'environment'])
                            ->model(fn (Get $get) => AssetMovement::find($get('asset_movement_id'))),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $assetMovement = AssetMovement::find($data['asset_movement_id'] ?? null);

        if ($assetMovement) {
            $this->form->saveRelationships();
        }

        Notification::make()
            ->title('Photos saved successfully.')
            ->success()
            ->send();
    }
}