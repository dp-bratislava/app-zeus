<?php

namespace App\Filament\Components;

use App\Models\Datahub\EmployeeContract as Contract;
use Closure;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

/**
 * Extends Filament Select component.
 * 
 * Presets label and filtering. If needed it can be overriden
 * and used like original Select component.
 * Both custom methods have to be called with null as input parameter
 * to apply custom bevaiour.  
 */
class ContractPicker extends Select
{
    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        if ($callback !== null) {
            $this->getOptionLabelFromRecordUsing = $callback;
        } else {
            $this->getOptionLabelFromRecordUsing = fn(Contract $record) => $record->formattedTitle . ' ' . $record->department->code;
        }

        return $this;
    }

    public function getSearchResultsUsing(?Closure $callback): static
    {
        if ($callback !== null) {
            $this->getSearchResultsUsing = $callback;
        } else {
            $this->getSearchResultsUsing = fn($search) =>
            Contract::query()
                ->where('pid', 'like', "%{$search}%")
                ->orWhereHas('employee', function (Builder $query) use ($search) {
                    $query
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })
                ->active()
                ->get()
                ->mapWithKeys(fn(Contract $contract) => [$contract->id => $contract->formattedTitle . ' - ' . $contract->department->code]);
        }

        return $this;
    }
}
