<?php

namespace App\Filament\Components;

use Closure;
use Dpb\Package\Fleet\Models\Vehicle;
use Filament\Forms\Components\Select;

/**
 * Extends Filament Select component.
 * 
 * Presets label and filtering. If needed it can be overriden
 * and used like original Select component.
 * Both custom methods have to be called with null as input parameter
 * to apply custom bevaiour.  
 */
class VehiclePicker extends Select
{
    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        if ($callback !== null) {
            $this->getOptionLabelFromRecordUsing = $callback;
        } else {
            $this->getOptionLabelFromRecordUsing = fn(Vehicle $record) => "{$record->code->code} {$record->model?->title}";
        }
        return $this;
    }

    // public function getSearchResultsUsing(?Closure $callback): static
    // {
    //     if ($callback !== null) {
    //         $this->getSearchResultsUsing = $callback;
    //     } else {
    //         $this->getSearchResultsUsing = fn($search) => 
    //             Vehicle::query()
    //                 ->whereHas('codes', function($q) use ($search) {
    //                     $q->whereLike('code', "%{$search}%")
    //                     ->orderByDesc('date_from')
    //                     ->first();
    //                 })
    //                 // ->orWhereLike('title', "%{$search}%")
    //                 ->get()
    //                 ->mapWithKeys(fn(Vehicle $vehicle) => [$vehicle->id => $vehicle->code->code . ' - ' . $vehicle->model->title]);
    //     }

    //     return $this;
    // }
    public function getSearchResultsUsing(?Closure $callback): static
    {
        if ($callback !== null) {
            $this->getSearchResultsUsing = $callback;
        } else {
            $this->getSearchResultsUsing = fn($search) =>
            Vehicle::query()
                ->whereHas('codes', function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%");
                })
                ->with(['codes' => fn($q) => $q->where('code', 'like', "%{$search}%")->orderByDesc('date_from')])
                ->get()
                ->mapWithKeys(function (Vehicle $vehicle) {
                    $latestCode = $vehicle->codes->first();
                    if (!$latestCode) {
                        return []; // important: return empty array if no code
                    }
                    return [
                        $vehicle->id => $latestCode->code . ' - ' . $vehicle->model?->title,
                    ];
                })
                ->toArray(); // must return array
        }

        return $this;
    }
}
