<?php

namespace App\Filament\Components;

use App\Models\Datahub\Employee;
use Closure;
use Filament\Forms\Components\Select;

/**
 * Extends Filament Select component.
 * 
 * Presets label and filtering. If needed it can be overriden
 * and used like original Select component.
 * Both custom methods have to be called with null as input parameter
 * to apply custom bevaiour.  
 */
class EmployeePicker extends Select
{
    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        if ($callback !== null) {
            $this->getOptionLabelFromRecordUsing = $callback;
        } else {
            $this->getOptionLabelFromRecordUsing = fn(Employee $record) => "{$record->primaryContractPid} {$record->last_name} {$record->first_name}";
        }

        return $this;
    }

    public function getSearchResultsUsing(?Closure $callback): static
    {
        if ($callback !== null) {
            $this->getSearchResultsUsing = $callback;
        } else {
            $this->getSearchResultsUsing = fn($search) =>
            Employee::query()
                ->leftJoin('datahub_employee_contracts', 'datahub_employees.id', '=', 'datahub_employee_contracts.datahub_employee_id')
                ->where('datahub_employees.first_name', 'LIKE', "%{$search}%")
                ->orWhere('datahub_employees.last_name', 'LIKE', "%{$search}%")
                // ->orWhere('datahub_employee_contracts.pid', $search)
                ->orWhere('datahub_employee_contracts.pid', 'LIKE', "%{$search}%")
                ->select('datahub_employees.*', 'datahub_employee_contracts.pid')
                ->active()
                ->get()
                ->mapWithKeys(fn(Employee $employee) => [$employee->id => $employee->pid . ' ' . $employee->fullName . ' - ' . $employee->primaryContract()?->department->code]);
        }

        return $this;
    }
}
