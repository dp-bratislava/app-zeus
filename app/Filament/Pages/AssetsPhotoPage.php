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

    /** Which finding mode the user is in: 'vehicle' (form) or 'recent' (list). */
    public string $findMode = 'vehicle';

    public function mount(): void
    {
        $this->form->fill();
    }

    /** Recent DEMONTAZ movements used in the "recent" finding tab. */
    public function getRecentDemontazesProperty(): Collection
    {
        return AssetMovement::query()
            ->with(['media', 'asset', 'taskItem.assetSlots.vehicle.model', 'taskItem.group'])
            ->where('movement_type', MovementType::DEMONTAZ->value)
            ->latest('created_at')
            ->limit(50)
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
                ];
            });
    }

    /** Open the photo-upload modal for a specific movement. */
    public function openMovementPhotos(int $id): void
    {
        $this->mountAction('photos', ['movement' => $id]);
    }

    /** Modal action that lets the user add / manage photos for a movement. */
    public function photosAction(): Action
    {
        return Action::make('photos')
            ->label('Upload Photos')
            ->icon('heroicon-m-photo')
            ->color('primary')
            ->modalHeading('Upload Photos')
            ->modalWidth('3xl')
            ->modalCancelActionLabel('Close')
            ->modalSubmitActionLabel('Save')
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
                        ->model($movement)
                        ->collection('movement-photos')
                        ->multiple()
                        ->image()
                        ->imagePreviewHeight('420')
                        ->panelLayout('grid')
                        ->reorderable()
                        ->downloadable()
                        ->openable()
                        ->extraInputAttributes(['accept' => 'image/*', 'capture' => 'environment']),
                ];
            })
            ->action(function (Action $action, Schema $schema): void {
                $schema->saveRelationships();

                // Let the page re-render so recent lists / history refresh.
                $this->dispatch('refresh-page');

                Notification::make()
                    ->title('Photos saved successfully.')
                    ->success()
                    ->send();
            });
    }

    /** Switch back to the form-based finder. */
    public function useVehicleForm(): void
    {
        $this->findMode = 'vehicle';
    }

    public function getHistoryProperty(): Collection
    {
        $mediaCollection = 'movement-photos';
        // Use the morph class so this matches however media records were stored.
        $morphClass = (new AssetMovement)->getMorphClass();

        // Fetch photos straight from the media table, grouped per movement.
        // This avoids depending on a "media" relationship on the AssetMovement
        // model (which is not defined on the package's model).
        $movementMedia = Media::query()
            ->where('collection_name', $mediaCollection)
            ->where('model_type', $morphClass)
            ->get()
            ->groupBy('model_id');

        if ($movementMedia->isEmpty()) {
            return collect();
        }

        $movementIds = $movementMedia->keys()->map(fn ($id) => (int) $id)->all();

        $movements = AssetMovement::query()
            ->with(['asset', 'taskItem'])
            ->whereIn('id', $movementIds)
            ->get()
            ->keyBy('id');

        return $movementMedia
            ->sortByDesc(function ($media, $movementId) use ($movements) {
                return $movements->get((int) $movementId)?->updated_at?->timestamp ?? 0;
            })
            ->take(30)
            ->map(function ($media, $movementId) use ($movements) {
                $movement = $movements->get((int) $movementId);

                $vehicleLabel = $movement?->taskItem?->assetSlots?->first()?->vehicle?->label;

                return [
                    'id' => (int) $movementId,
                    'label' => trim(
                        '#'.$movementId.' '
                        . ($movement?->asset_serial_number ?? $movement?->asset?->serial_number ?? '')
                    ),
                    'date' => $movement?->date?->format('d.m.Y'),
                    'updated_at' => $movement?->updated_at,
                    'task_item_id' => $movement?->task_item_id,
                    'task_item_title' => $movement?->taskItem?->title
                        ?? ('Task #'.($movement?->task_item_id ?? '?')),
                    'vehicle_label' => $vehicleLabel,
                    'photos' => $media->sortBy('id')->map(fn (Media $item) => [
                        'id' => $item->id,
                        'url' => $item->getUrl(),
                    ])->values(),
                ];
            })
            ->values();
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
            ])
            ->statePath('data');
    }
}