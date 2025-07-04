<?php

namespace App\Filament\Components;

use App\Models\Datahub\Department;
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
class DepartmentPicker extends Select
{
    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        if ($callback !== null) {
            $this->getOptionLabelFromRecordUsing = $callback;
        } else {
            $this->getOptionLabelFromRecordUsing = fn(Department $record) => "{$record->code} {$record->title}";
        }
        return $this;
    }

    public function getSearchResultsUsing(?Closure $callback): static
    {
        if ($callback !== null) {
            $this->getSearchResultsUsing = $callback;
        } else {
            $this->getSearchResultsUsing = fn($search) => 
                Department::query()
                    ->where('code', 'like', "%{$search}%")
                    ->orWhereLike('title', "%{$search}%")
                    ->get()
                    ->mapWithKeys(fn(Department $department) => [$department->id => $department->code . ' - ' . $department->title]);
        }

        return $this;
    }
}
