<?php

namespace App\Filament\Pages;

use Dpb\MasterPermissionGuard\Concerns\HasPageGuard;
use Dpb\Package\Assets\Models\AssetMovement;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Tasks\Models\TaskItem;
use Filament\Actions\Action;
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
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Dpb\WtfTmsBridge\Enums\MovementType;
use Dpb\Package\TaskMS\UI\Filament\Components\FleetVehiclePicker;
use Dpb\Package\TaskMS\Services\SelectOptions\VehiclePickerOptions;

class AssetsPhotoPage extends Page implements HasForms
{
    use HasPageGuard;
    use InteractsWithForms;

    protected string $view = 'filament.pages.assets-photo-page';

    protected static ?string $navigationLabel = 'Foto';

    protected static ?string $title = '';

    public ?array $data = [];

    /** Which finding mode the user is in: 'vehicle' or 'recent'. */
    public string $findMode = 'recent';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getRecentDemontazesProperty(): Collection
    {
        return AssetMovement::query()
            ->with(['media', 'asset', 'taskItem.assetSlots.vehicle.model', 'taskItem.group'])
            ->where('movement_type', MovementType::DEMONTAZ->value)
            ->latest('created_at')
            ->limit(20)
            ->get()
            ->map(function (AssetMovement $movement): array {
                $vehicle = $movement->taskItem?->assetSlots?->first()?->vehicle;

                return [
                    'id' => $movement->id,
                    'date' => $movement->date?->format('d.m.Y'),
                    'created_at' => $movement->created_at,
                    'vehicle_label' => $vehicle?->label,
                    'vehicle_model' => $vehicle?->model?->title,
                    'task_item_group' => $movement->taskItem?->group?->title,
                    'photo_count' => $movement->getMedia('movement-photos')->count(),
                    'slot_label' => $movement->slotContext?->label,
                ];
            });
    }

    public function openMovementPhotos(int $id): void
    {
        $this->mountAction('photos', ['movement' => $id]);
    }

    public function photosAction(): Action
    {
        return Action::make('photos')
            ->label('Nahratať fotky')
            ->icon('heroicon-m-photo')
            ->color('primary')
            ->modalHeading('Nahratať fotky')
            ->modalWidth('3xl')
            ->modalCancelActionLabel('Zatvoriť')
            ->modalSubmitActionLabel('Uložiť')
            ->mountUsing(function (Action $action, Schema $schema): void {
                $movement = AssetMovement::with('media')->find(
                    $action->getArguments()['movement'] ?? null,
                );

                $schema->fill([
                    'photos_upload' => $movement?->getMedia('movement-photos')
                        ->mapWithKeys(fn (Media $media): array => [
                            $media->getAttributeValue('uuid') => $media->getAttributeValue('uuid'),
                        ])
                        ->toArray() ?? [],
                ]);
            })
            ->schema(function (Action $action): array {
                $movement = AssetMovement::find($action->getArguments()['movement'] ?? null);

                return [
                    SpatieMediaLibraryFileUpload::make('photos_upload')
                        ->hiddenLabel()
                        ->model($movement)
                        ->collection('movement-photos')
                        ->multiple()
                        ->image()
                        ->imagePreviewHeight('420')
                        ->panelLayout('grid')
                        ->reorderable()
                        ->downloadable()
                        ->openable()
                        ->placeholder('Kliknite sem pre pridanie fotky')
                        ->extraInputAttributes(['accept' => 'image/*', 'capture' => 'environment']),
                ];
            })
            ->action(function (Action $action, Schema $schema): void {
                $schema->saveRelationships();
                $this->dispatch('refresh-page');

                Notification::make()
                    ->title('Fotky boli úspešne uložené.')
                    ->success()
                    ->send();
            });
    }

    public function useVehicleForm(): void
    {
        $this->findMode = 'vehicle';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('')
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
                            ->label('Podzákazka')
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
                                        $taskItem->id => (string) ("#{$taskItem->id} " . $taskItem->description ),
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
                            ->label('Demontáž')
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
                                        $label = $movement->slotContext->label . ' ' . ($movement->asset_serial_number ?? '');

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
            ])
            ->statePath('data');
    }
}