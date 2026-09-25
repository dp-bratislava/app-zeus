<?php

namespace Dpb\Modules\Tasks\Filament\Pages;

use Dpb\Departments\Concerns\HasDepartmentService;
use Dpb\DpbUtils\Utils\FilamentPageConfigurator;
use Dpb\MasterPermissionGuard\Concerns\HasPageGuard;
use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\UiComponents\Concerns\HasDatePickerComponent;
use Dpb\UiComponents\Concerns\InteractsWithModalErrorComponent;
use Dpb\WorkTimeFund\Exceptions\WorkAssignmentExceptions\WorkAssignmentValidationException;
use Dpb\WorkTimeFund\Models\Operation;
use Dpb\WorkTimeFund\Services\RetroactiveEditService;
use Dpb\WorkTimeFundFilament\Filament\Pages\WorktimeManagementPage;
use Dpb\Modules\Tasks\Commands\AssignWorkToTaskBatchCommand;
use Dpb\Modules\Tasks\Helpers\Base64UrlHelper;
use Dpb\Modules\Tasks\Models\WtfCategory as Category;
use Dpb\Modules\Tasks\Workflows\AssignWorkToTaskBatchWorkflow;
use Dpb\WtfUi\FilamentComponents\AssignmentContainer\AssignmentRequestDTO;
use Dpb\WtfUi\FilamentComponents\AssignmentContainer\HasAssignmentContainer;
use Dpb\WtfUi\LegacyBridges\EphemeralWorkAssignmentGeneratorAdapter;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Livewire\Attributes\Computed;
use Override;

class TaskBatchWorkOrdersPage extends Page implements HasActions, HasForms
{
    use FilamentPageConfigurator;
    use HasAssignmentContainer;
    use HasDatePickerComponent;
    use HasDepartmentService;
    use HasPageGuard;
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithModalErrorComponent;

    private const DATEPICKER_MULTI_MODE = false;

    public ?int $batchId = null;
    public ?int $taskBatchId = null;
    public array $taskItemIds = [];

    public array $selectedEmployees = [];

    public ?int $selectedCategoryId = null;

    public ?int $selectedTaskItemGroupId = null;

    public array $demandedDates;

    public function mount(): void
    {
        $this->demandedDates = $this
            ->getDatePickerStateService()
            ->getDates(multi: static::DATEPICKER_MULTI_MODE)
            ->toArray();
        $encodedTaskItemIds = Request::instance()->get(key: 'taskItems')
            ?? throw new \RuntimeException(message: 'taskItems parameter is required');
        $base64DecodedJson = Base64UrlHelper::decode($encodedTaskItemIds);
        $this->taskItemIds = json_decode($base64DecodedJson, true);
        $this->batchId = Request::instance()->get(key: 'batchId')
            ?? throw new \RuntimeException(message: 'batchId parameter is required');
        $this->taskBatchId = Request::instance()->get(key: 'taskBatchId')
            ?? throw new \RuntimeException(message: 'taskBatchId parameter is required');
        $this->selectedTaskItemGroupId = $this->taskItemGroups->first()?->id;
        $this->getAssignmentService()->forget();
    }

    #[Override]
    protected function handleDatePickerStateChanged(array $datePickerState): void
    {
        $this->demandedDates = $datePickerState;
        $this->selectedEmployees = [];
    }

    #[Computed]
    public function taskItems(): EloquentCollection
    {
        return TaskItem::query()
            ->with(relations: ['task.group', 'group'])
            ->whereIn(column: 'id', values: $this->taskItemIds ?: [0])
            ->get();
    }

    #[Computed]
    public function taskItemGroups(): Collection
    {
        return $this->taskItems
            ->pluck(value: 'group')
            ->unique();
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::query()
            ->whereHas(
                relation: 'taskItemGroups',
                callback: fn (
                    Builder $q
                ): Builder => $q->whereKey(
                    id: $this->selectedTaskItemGroupId
                )
            )
            ->whereHas(
                relation: 'departments',
                callback: fn(
                    Builder $q
                ): Builder => $q->whereKey(id: $this->getDepartmentService()->getActiveDepartment()->id)
            )
            ->where(column: 'type', operator: '=', value: 'daily-maintenance')
            ->get()
            ?? collect();
    }

    #[Computed]
    public function operations(): Collection
    {
        return Operation::query()
            ->whereHas(
                relation: 'parent',
                callback: fn (
                    Builder $q
                ): Builder => $q->whereKey(id: $this->selectedCategoryId)
            )
            ->get()
            ?? collect();
    }

    public function isCategoryOpened(
        Category $category
    ): bool {
        return $category->id === $this->selectedCategoryId;
    }

    public function openCategory(
        Category $category
    ): void {
        $this->selectedCategoryId = $category->id;
    }

    public function selectTaskItemGroup(
        int $taskItemGroupId
    ): void {
        $this->selectedTaskItemGroupId = $taskItemGroupId;
    }

    public function attachOperation(
        Operation $operation
    ): void {
        try {
            $demandedDates = $this->getDatePickerStateService()->getDates(multi: static::DATEPICKER_MULTI_MODE)->toArray();
            $this->validateDemandedDates(demandedDates: $demandedDates);
            $this->addAssignmentRequest(
                assignmentRequest: EphemeralWorkAssignmentGeneratorAdapter::createAssignmentRequestDTO(
                    employeePids: $this->selectedEmployees,
                    dates: $demandedDates,
                    activityRecordSource: $operation,
                    specialRules: "task_item_group_id_{$this->selectedTaskItemGroupId}"
                )
            );
        } catch (WorkAssignmentValidationException $ex) {
            $this->dispatchErrorModal(description: $ex->getMessage());
        } catch (Exception $ex) {
            App::make(ExceptionHandler::class)
                ->report($ex);
            $this->dispatchErrorModal(description: $ex->getMessage());
        }
    }

    public function taskBatchAssignAction(): Action
    {
        return Action::make(name: 'taskBatchAssign')
            ->label(label: 'TBA')
            ->action(action: function (AssignWorkToTaskBatchWorkflow $wfl): void {
                try {
                    $wfl->handle(new AssignWorkToTaskBatchCommand(
                        batchId: $this->batchId,
                        taskBatchId: $this->taskBatchId,
                    ));
                    $this->redirect(url: WorktimeManagementPage::getUrl());
                } catch (\RuntimeException $e) {
                    $message = $e->getMessage();

                    if (str_contains($message, 'Shift is not set')) {
                        $message = 'Niektorý zo zvolených zamestnancov nemá na daný deň plánovanú smenu.';
                    }

                    Notification::make()
                        ->title($message)
                        ->danger()
                        ->send();
                }
            });
    }

    public function submitWorkOrderAction(): Action
    {
        return Action::make(name: 'submitWorkOrder')
            ->label(label: 'Uložiť')
            ->action(action: function (): void {
                try {
                    EphemeralWorkAssignmentGeneratorAdapter::storeDailyMaintenanceAssignmentRequests(
                        taskItems: $this->taskItems
                    );
                    $this->redirect(url: WorktimeManagementPage::getUrl());
                } catch (\RuntimeException $e) {
                    $message = $e->getMessage();

                    if (str_contains($message, 'Shift is not set')) {
                        $message = 'Niektorý zo zvolených zamestnancov nemá na daný deň plánovanú smenu.';
                    }

                    Notification::make()
                        ->title($message)
                        ->danger()
                        ->send();
                }
            });
    }

    public function getCountOfAssignedOperations(
        int $taskItemGroupId
    ): int {
        return $this->getAssignmentRequests()
            ->filter(
                fn (
                    AssignmentRequestDTO $assignmentRequest
                ): bool => $taskItemGroupId === $assignmentRequest
                    ->activityDefinition
                    ->taskItemGroup?->id
            )
            ->count();
    }

    private function validateDemandedDates(
        array $demandedDates
    ) {
        $retroactiveEditService = App::make(RetroactiveEditService::class);
        $forbiddenDates = array_filter($demandedDates, fn ($date) => ! $retroactiveEditService->isWithinGracePeriod($date));
        empty($forbiddenDates)
            || throw new \RuntimeException(
                trans_choice(
                    'wtf-tms-bridge-ui::pages/daily-maintenance-work-order.validation.errors.forbidden_backdate',
                    count($forbiddenDates),
                    ['dates' => implode(', ', $forbiddenDates)]
                )
            );
    }

    // Page configuration

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static function getConfigKey(): string
    {
        return 'dpb-wtf-tms-bridge.filament_pages.task_batch_work_orders';
    }
}
