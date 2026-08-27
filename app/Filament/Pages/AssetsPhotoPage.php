<?php

namespace App\Filament\Pages;

use Dpb\MasterPermissionGuard\Concerns\HasPageGuard;
use Dpb\WtfTmsBridge\Models\AssetMovement;
use Dpb\WtfTmsBridge\Models\Photo;
use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\Package\TaskMS\Models\TaskAssignment;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Illuminate\Support\Collection;
use Dpb\WtfTmsBridge\Enums\MovementType;
use Dpb\Package\Tasks\Models\Task;

class AssetsPhotoPage extends Page implements HasForms
{
    use HasPageGuard;
    use InteractsWithForms;

    protected string $view = 'filament.pages.assets-photo-page';

    protected static ?string $navigationLabel = 'Fotky';

    public ?array $taskData = [];
    public string $findMode = 'agregaty';

    protected $listeners = ['photos-changed' => 'resetDemontazScroll'];

    public int $accidentOffset = 0;
    public array $accidents = [];
    public bool $hasMoreAccidents = true;

    public int $demontazOffset = 0;
    public array $demontazes = [];
    public bool $hasMoreDemontazes = true;

    public const ACCIDENTS_PER_PAGE = 12;
    public const DEMONTAZES_PER_PAGE = 12;

    private array $inactiveStates = ['closed', 'cancelled', 'closed-with-malfunction'];

    public function mount(): void
    {
        $this->resetAccidentScroll();
        $this->resetDemontazScroll();
    }

    public function getTitle(): string
    {
        return '';
    }

    private function paginatedDemontazes(): Collection
    {
        $movements = AssetMovement::query()
            ->with(['asset', 'taskItem.assetSlots.vehicle.model', 'taskItem.group'])
            ->where('movement_type', MovementType::DEMONTAZ->value)
            ->latest('created_at')
            ->skip($this->demontazOffset)
            ->limit(static::DEMONTAZES_PER_PAGE)
            ->get();

        $photoCounts = $this->photoCountsFor(AssetMovement::class, $movements->pluck('id'), 'movement-photos');

        return $movements->map(function (AssetMovement $movement) use ($photoCounts): array {
            $vehicle = $movement->taskItem?->assetSlots?->first()?->vehicle;
            return [
                'id' => $movement->id,
                'date' => $movement->date?->format('d.m.Y'),
                'created_at' => $movement->created_at,
                'vehicle_label' => $vehicle?->label,
                'vehicle_model' => $vehicle?->model?->title,
                'task_item_group' => $movement->taskItem?->group?->title,
                'photo_count' => $photoCounts[$movement->id] ?? 0,
                'slot_label' => $movement->slotContext?->label,
            ];
        });
    }

    public function loadMoreDemontazes(): void
    {
        $this->demontazOffset += static::DEMONTAZES_PER_PAGE;
        $page = $this->paginatedDemontazes();

        if ($page->isEmpty()) {
            $this->hasMoreDemontazes = false;
            return;
        }

        $this->demontazes = array_merge($this->demontazes, $page->all());
    }

    public function resetDemontazScroll(): void
    {
        $this->demontazOffset = 0;
        $this->hasMoreDemontazes = true;
        $this->demontazes = $this->paginatedDemontazes()->all();
    }

    private function photoCountsFor(string $photoableType, $ids, string $collection): array
    {
        $ids = collect($ids)->unique()->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $type = Photo::typeFor(new $photoableType);

        return Photo::query()
            ->selectRaw('photoable_id, count(*) as total')
            ->where('photoable_type', $type)
            ->whereIn('photoable_id', $ids)
            ->where('collection', $collection)
            ->groupBy('photoable_id')
            ->pluck('total', 'photoable_id')
            ->map(fn ($n) => (int) $n)
            ->all();
    }

    public function selectAccident(int $taskId): void
    {
        $this->taskData['task_id'] = $taskId;
        $this->findMode = 'task';
    }

    public function showAccidents(): void
    {
        $this->taskData = [];
        $this->resetAccidentScroll();
        $this->findMode = 'accidents';
    }

    public function loadMoreAccidents(): void
    {
        $this->accidentOffset += static::ACCIDENTS_PER_PAGE;
        $page = $this->paginatedAccidents();

        if ($page->isEmpty()) {
            $this->hasMoreAccidents = false;
            return;
        }

        $this->accidents = array_merge($this->accidents, $page->all());
    }

    public function resetAccidentScroll(): void
    {
        $this->accidentOffset = 0;
        $this->hasMoreAccidents = true;
        $this->accidents = $this->paginatedAccidents()->all();
    }

    private function paginatedAccidents(): Collection
    {
        $tasks = Task::query()
            ->whereHas('items', fn ($q) => $q->whereNotIn('state', $this->inactiveStates))
            ->whereHas('group', fn ($q) => $q->where('code', 'accident'))
            ->with(['items.group'])
            ->latest('id')
            ->skip($this->accidentOffset)
            ->limit(static::ACCIDENTS_PER_PAGE)
            ->get();

        $itemIds = collect();
        foreach ($tasks as $task) {
            foreach ($task->items as $item) {
                if (!in_array($item->state, $this->inactiveStates, true)) {
                    $itemIds->push($item->id);
                }
            }
        }
        $itemIds = $itemIds->unique()->values();

        $photoCounts = $this->photoCountsFor(TaskItem::class, $itemIds, 'task-item-photos');

        $assignments = TaskAssignment::query()
            ->whereIn('task_id', $tasks->pluck('id'))
            ->get()
            ->keyBy('task_id');

        $vehicles = $assignments
            ->map(fn ($a) => $a->subject)
            ->filter(fn ($s) => $s instanceof Vehicle)
            ->unique('id');
        $vehicles->load(['model', 'codes', 'licencePlates']);
        $vehicles = $vehicles->keyBy('id');

        return $tasks->map(function (Task $task) use ($photoCounts, $assignments, $vehicles): array {
            $assignment = $assignments->get($task->id);
            $vehicle = $assignment && $assignment->subject instanceof Vehicle
                ? $vehicles->get($assignment->subject->getKey())
                : null;
            $itemIds = $task->items
                ->filter(fn ($i) => !in_array($i->state, $this->inactiveStates, true))
                ->pluck('id');

            $photoCount = 0;
            foreach ($itemIds as $itemId) {
                $photoCount += $photoCounts[$itemId] ?? 0;
            }

            return [
                'id' => $task->id,
                'date' => $task->date?->format('d.m.Y'),
                'vehicle_label' => $vehicle?->label ?? $task->title ?? ('Zákazka #' . $task->id),
                'vehicle_model' => $vehicle?->model?->title,
                'group_title' => $task->group?->title,
                'item_count' => $itemIds->count(),
                'photo_count' => $photoCount,
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
            ->modalWidth('screen-2xl')
            ->modalCancelActionLabel('Ok')
            ->modalSubmitAction(false)
            ->schema(function (Action $action): array {
                $movement = AssetMovement::find($action->getArguments()['movement'] ?? null);

                if (!$movement) {
                    return [];
                }

                return [
                    Livewire::make(
                        'dpb.wtftmsbridge.photo-gallery',
                        data: [
                            'photoableType' => AssetMovement::class,
                            'photoableId' => $movement->id,
                            'collection' => 'movement-photos',
                            'withBufferPicker' => true,
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
            ->modalWidth('screen-2xl')
            ->modalCancelActionLabel('Ok')
            ->modalSubmitAction(false)
            ->schema(function (Action $action): array {
                $taskItem = TaskItem::find($action->getArguments()['taskItem'] ?? null);

                if (!$taskItem) {
                    return [];
                }

                return [
                    Livewire::make(
                        'dpb.wtftmsbridge.photo-gallery',
                        data: [
                            'photoableType' => TaskItem::class,
                            'photoableId' => $taskItem->id,
                            'collection' => 'task-item-photos',
                            'withBufferPicker' => true,
                        ],
                    ),
                ];
            });
    }

    public function showAgregaty(): void
    {
        $this->findMode = 'agregaty';
    }

    public function showBuffer(): void
    {
        $this->findMode = 'buffer';
    }

    public function getSelectedTaskItemsProperty(): Collection
    {
        $taskId = $this->taskData['task_id'] ?? null;

        if (blank($taskId)) {
            return collect();
        }

        $taskItems = TaskItem::query()
            ->where('task_id', $taskId)
            ->with(['group'])
            ->orderBy('id')
            ->get();

        $photos = Photo::query()
            ->where('photoable_type', TaskItem::class)
            ->whereIn('photoable_id', $taskItems->pluck('id'))
            ->where('collection', 'task-item-photos')
            ->orderBy('id')
            ->get()
            ->groupBy('photoable_id');

        return $taskItems->map(fn (TaskItem $taskItem) => [
            'id' => $taskItem->id,
            'group_title' => $taskItem->group?->title,
            'photos' => collect($photos[$taskItem->id] ?? [])
                ->map(fn (Photo $photo) => [
                    'id' => $photo->id,
                    'url' => $photo->url,
                    'name' => $photo->original_name,
                ])
                ->values(),
            'photo_count' => ($photos[$taskItem->id] ?? collect())->count(),
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