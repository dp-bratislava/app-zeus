<?php

namespace App\Jobs\Reports;

use App\Filament\Exports\Reports\CustomWorkActivityReportExporter;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportWorkActivityReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $filters,
        public string $fileName,
        public int $userId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(CustomWorkActivityReportExporter $exporter): void
    {
        $path = storage_path("app/exports/{$this->fileName}");

        $user = User::find($this->userId);
        $export = $exporter->generate($this->filters, $path, $this->userId);


        Notification::make()
            ->title(__('reports/export.export_finished.title'))
            ->body(__('reports/export.export_finished.body', ['filename' => $this->fileName]))
            ->success()
            ->actions([
                Action::make('download')
                    ->label(__('reports/export.actions.download'))
                    ->url(route('exports.download', $export))
                    ->openUrlInNewTab(),
            ])
            ->sendToDatabase($user);
    }
}
