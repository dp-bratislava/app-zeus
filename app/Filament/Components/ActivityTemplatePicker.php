<?php

namespace App\Filament\Components;

use Dpb\Packages\Activities\Models\ActivityTemplate;
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
class ActivityTemplatePicker extends Select
{
    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        if ($callback !== null) {
            $this->getOptionLabelFromRecordUsing = $callback;
        } else {
            $this->getOptionLabelFromRecordUsing = fn(ActivityTemplate $record) => "{$record->code} {$record->title}";
        }
        return $this;
    }

    public function getSearchResultsUsing(?Closure $callback): static
    {
        if ($callback !== null) {
            $this->getSearchResultsUsing = $callback;
        } else {
            $this->getSearchResultsUsing = fn($search) => 
                ActivityTemplate::query()
                    ->where('code', 'like', "%{$search}%")
                    ->orWhereLike('title', "%{$search}%")
                    ->get()
                    ->mapWithKeys(fn(ActivityTemplate $template) => [$template->id => $template->code . ' - ' . $template->title]);
        }

        return $this;
    }
}
