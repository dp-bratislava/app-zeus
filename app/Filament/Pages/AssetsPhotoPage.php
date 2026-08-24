<?php

namespace App\Filament\Pages;

use Dpb\MasterPermissionGuard\Concerns\HasPageGuard;
use Dpb\WtfTmsBridge\Models\AssetMovement;
use Dpb\WtfTmsBridge\Models\Photo;
use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Dpb\WtfTmsBridge\Enums\MovementType;
use Dpb\Package\Tasks\Models\Task;

class AssetsPhotoPage extends Page implements HasForms
{
    use HasPageGuard;
    use InteractsWithForms;

    protected string $view = 'filament.pages.assets-photo-page';

    protected static ?string $navigationLabel = 'Fotky';

    protected static ?string $title = '';

    public ?array $taskData = [];
    /** Which finding mode the user is in: 'recent' or 'task'. */
    public string $findMode = 'recent';

    private function inactiveTaskItemStates(): array
    {
        return ['closed', 'cancelled', 'closed-with-malfunction'];
    }

    public function mount(): void
    {
        $this->taskForm->fill();
    }

    public function getRecentDemontazesProperty(): Collection
    {
        return AssetMovement::query()
            ->with(['asset', 'taskItem.assetSlots.vehicle.model', 'taskItem.group'])
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
                    'photo_count' => Photo::query()->for($movement, 'movement-photos')->count(),
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
            ->label('Nahrať fotky')
            ->icon('heroicon-m-photo')
            ->color('primary')
            ->modalHeading('Nahrať fotky')
            ->modalWidth('7xl')
            ->modalCancelActionLabel('Ok')
            ->modalSubmitAction(false)
            ->schema(function (Action $action): array {
                $movement = AssetMovement::find($action->getArguments()['movement'] ?? null);

                if (! $movement) {
                    return [];
                }

                return [
                    Livewire::make(
                        'dpb.wtftmsbridge.photo-gallery',
                        data: [
                            'photoableType' => AssetMovement::class,
                            'photoableId' => $movement->id,
                            'collection' => 'movement-photos',
                        ],
                    ),
                ];
            });
    }

    public function openTaskItemPhotos(int $id): void
    {
        $this->mountAction('taskPhotos', ['taskItem' => $id]);
    }

    public function taskPhotosAction(): Action
    {
        return Action::make('taskPhotos')
            ->label('Nahrať fotky')
            ->icon('heroicon-m-photo')
            ->color('primary')
            ->modalHeading('Nahrať fotky k podzákazke')
            ->modalWidth('7xl')
            ->modalCancelActionLabel('Ok')
            ->modalSubmitAction(false)
            ->schema(function (Action $action): array {
                $taskItem = TaskItem::find($action->getArguments()['taskItem'] ?? null);

                if (! $taskItem) {
                    return [];
                }

                return [
                    Livewire::make(
                        'dpb.wtftmsbridge.photo-gallery',
                        data: [
                            'photoableType' => TaskItem::class,
                            'photoableId' => $taskItem->id,
                            'collection' => 'task-item-photos',
                        ],
                    ),
                ];
            });
    }

    public function useTaskForm(): void
    {
        $this->findMode = 'task';
    }

    public function taskForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('')
                    ->schema([
                        Select::make('task_id')
                            ->label('Zákazka')
                            ->placeholder('Vyberte zákazku s aktívnymi podzákazkami')
                            ->options(fn () => $this->activeTaskOptions())
                            ->preload()
                            ->searchable()
                            ->live()
                            ->getSearchResultsUsing(fn (string $search) => $this->activeTaskOptions($search))
                            ->allowHtml()
                            ->required(),
                    ]),
            ])
            ->statePath('taskData');
    }

    private function activeTaskOptions(string $search = ''): array
    {
        $tasks = Task::query()
            ->whereHas('items', fn ($q) => $q->whereNotIn('state', $this->inactiveTaskItemStates()))
            ->whereHas('group', fn ($q) => $q->where('code', 'accident'))
            ->latest('id')
            ->limit(200)
            ->get();

        $assignments = TaskAssignment::query()
            ->whereIn('task_id', $tasks->pluck('id'))
            ->get()
            ->keyBy('task_id');

        $vehicles = $assignments
            ->map(fn ($a) => $a->subject)
            ->filter(fn ($s) => $s instanceof Vehicle)
            ->unique('id');
        $vehicles->load(['model', 'codes', 'licencePlates']);

        $options = [];
        foreach ($tasks as $task) {
            $assignment = $assignments->get($task->id);
            $vehicle = $assignment?->subject instanceof Vehicle ? $assignment->subject : null;

            $vehicleLabel = $vehicle ? trim((string) $vehicle->label) : '';
            $vehicleModel = $vehicle ? trim((string) $vehicle->model?->title) : '';

            $taskDate = $task->date?->format('d.m.Y');
            $vehicleText = trim("Vozidlo: " . trim((string) $task->title) . " {$vehicleLabel} {$vehicleModel}");

            $optionText = trim("{$vehicleText} {$taskDate}");

            if (filled($search) && ! str_contains(mb_strtolower($optionText), mb_strtolower($search))) {
                continue;
            }
            $options[$task->id] = sprintf(
                '<div class="filament-option">
                    <div>%s</div>
                    <div class="flex items-center gap-2 text-xs text-gray-400">
                        <span>ID: %d</span>
                        %s
                    </div>
                </div>',
                e($vehicleText),
                $task->id,
                $taskDate ? '<span>· ' . e($taskDate) . '</span>' : '',
            );
        }

        return $options;
    }

    public function getSelectedTaskItemsProperty(): Collection
    {
        $taskId = $this->taskData['task_id'] ?? null;

        if (blank($taskId)) {
            return collect();
        }

        return TaskItem::query()
            ->where('task_id', $taskId)
            ->with(['group'])
            ->orderBy('id')
            ->get()
            ->map(fn (TaskItem $taskItem) => [
                'id' => $taskItem->id,
                'group_title' => $taskItem->group?->title,
                'photos' => Photo::query()
                    ->for($taskItem, 'task-item-photos')
                    ->orderBy('id')
                    ->get()
                    ->map(fn (Photo $photo) => [
                        'id' => $photo->id,
                        'url' => $photo->url,
                        'name' => $photo->original_name,
                    ])
                    ->values(),
                'photo_count' => Photo::query()->for($taskItem, 'task-item-photos')->count(),
            ]);
    }

    public function getSelectedTaskInfoProperty(): ?array
    {
        $taskId = $this->taskData['task_id'] ?? null;

        if (blank($taskId)) {
            return null;
        }

        $assignment = TaskAssignment::query()
            ->with(['task.group'])
            ->where('task_id', $taskId)
            ->first();

        $vehicle = $assignment?->subject;
        if ($vehicle instanceof Vehicle) {
            $vehicle->loadMissing(['model', 'codes', 'licencePlates']);
        }

        return [
            'vehicle_label' => $vehicle?->label ?? 'N/A',
            'vehicle_model' => $vehicle?->model?->title ?? 'N/A',
            'group_title' => $assignment?->task?->group?->title ?? 'N/A',
        ];
    }
}

