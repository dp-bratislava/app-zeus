<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Schemas;

use App\Models\Datahub\EmployeeContract;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

class TaskBatchForm
{
    public const COL_NAME_VEHICLE_PICKER = 'vehicles';
    public const COL_NAME_CONTRACT_PICKER = 'contracts';
    public const COL_NAME_OPERATION_PICKER = 'operations';
    public const COL_NAME_VEHICLE_TYPE_PICKER = 'vehicle_type_id';
    public const COL_NAME_ASSIGNED_TO = 'assigned_to_id';
    public const COL_NAME_TG_PICKER = 'task-groups';
    public const COL_NAME_TIG_PICKER = 'task-item-groups';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...TaskStepSchema::make(),
            ]);
    }

    private static function contractsSection()
    {
        return Section::make(__('dpb-mod-tasks::daily-maintenance.form.fields.contracts'))
            // ->label(__())
            ->label(__('dpb-mod-tasks::daily-maintenance.form.fields.contracts'))
            ->description('TO DO: toto by malo byť do budúcna prepojené s fondom pracovného času')
            ->schema([
                CheckboxList::make('contracts')
                    ->label(__('dpb-mod-tasks::daily-maintenance.form.fields.contracts'))
                    ->options(
                        fn(): Collection => EmployeeContract::with('employee')
                            ->workers()
                            ->byDepartment('5400')
                            ->get()
                            ->mapWithKeys(fn($contract) => [$contract->id => join(' ', [$contract->pid, $contract->employee->last_name])])
                    )

                    ->searchable()
                    ->bulkToggleable()
                    ->columns(3)
            ]);
    }
}
