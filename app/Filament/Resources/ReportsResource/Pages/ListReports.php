<?php

namespace App\Filament\Resources\ReportsResource\Pages;

use App\Filament\Resources\ReportsResource;
use App\Reports\ReportFactory;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Notifications\Notification;
use App\Jobs\Reports\ExportReportJob;
use Illuminate\Support\Facades\Blade;

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

        return new \Illuminate\Support\HtmlString(Blade::render('
        <div class="flex items-center justify-between gap-x-4">
            <div class="flex items-center gap-x-3">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Zvoľte typ reportu:</label>
                <select onchange="{!! $onChange !!}" 
                    class="fi-select-input block w-full rounded-lg border-none bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 focus:ring-2 focus:ring-primary-600 dark:bg-white/5 dark:ring-white/20">
                    {!! $optionsHtml !!}
                </select>
            </div>

            <x-filament::button 
                wire:click="runExport" 
                icon="heroicon-m-arrow-down-tray"
                color="primary"
            >
                Exportovať Report
            </x-filament::button>
        </div>
    ', [
        'onChange' => $onChange,
        'optionsHtml' => $optionsHtml,
    ]));
}
    public function runExport(): void
{
    // Get the driver based on current selection
    $driver = ReportFactory::make($this->currentReportType);
    
    // Get filter state directly from the table form
    $filters = $this->getTableFiltersForm()->getState();

    $exporter = $driver->getExporter();
    $filename = $driver->generateExportFilename();

    ExportReportJob::dispatch(
        new $exporter(),
        $filters,
        $filename,
        auth()->id(),
    );

    Notification::make()
        ->title(__('reports/export.export_started.title'))
        ->body(__('reports/export.export_started.body'))
        ->success()
        ->send();
}


}


