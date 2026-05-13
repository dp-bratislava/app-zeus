<?php

namespace App\Filament\Resources\ReportsResource\Pages;

use App\Filament\Resources\ReportsResource;
use App\Reports\ReportFactory;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListReports extends ListRecords
{
    protected static string $resource = ReportsResource::class;
    
    public ?string $currentReportType = null;

    public function mount(): void
    {
        parent::mount();
        // Initialize from query parameter or default
        $this->currentReportType = request()->query('report', 'work-activity');
    }

    public function getTitle(): string | Htmlable
    {
        return '';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getSubHeading(): string | Htmlable | null
    {
        $currentReport = $this->currentReportType;
        $reports = ReportFactory::getAvailable();
        
        $options = collect($reports)->mapWithKeys(fn($driver, $key) => [
            $key => $driver->name()
        ])->toArray();

        $onChange = <<<'JS'
            const value = event.target.value;
            window.location.href = window.location.pathname + '?report=' + value;
        JS;

        $optionsHtml = collect($options)->map(function($label, $key) use ($currentReport) {
            $selected = $currentReport === $key ? 'selected' : '';
            return "<option value=\"{$key}\" {$selected}>{$label}</option>";
        })->join('');

        return new \Illuminate\Support\HtmlString(
            "<div style='display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;'>
                <label style='font-weight: 500; color: #374151;'>Zvoľte typ reportu:</label>
                <select onchange=\"{$onChange}\" 
                    style='padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;'>
                    {$optionsHtml}
                </select>
            </div>"
        );
    }
}


