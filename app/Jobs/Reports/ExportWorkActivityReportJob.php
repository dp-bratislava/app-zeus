<?php

namespace App\Jobs\Reports;

use App\Filament\Exports\Reports\WorkActivityReportExporter;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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


    public function handle(WorkActivityReportExporter $exporter)
    {
        // 1. Get Data from DB
        // $userData = User::all(['lohin', 'email'])->toArray();
        // $payload = [
        //     'header' => ['Name', 'Email'],
        //     'data' => [
        //         ['name' => 'u1', 'e' => 'u1@my.com'],
        //         ['name' => 'u2', 'e' => 'u2@my.com'],
        //         ['name' => 'u3', 'e' => 'u3@my.com'],
        //         ['name' => 'u4', 'e' => 'u4@my.com'],
        //     ]
        // ];

        $exporter->run($this->filters);

//         // 2. Send to Microservice
//         $exportUrl = 'http://10.10.11.224:8111/export';
//         // $response = Http::post('http://10.10.11.224:8111/export', [
//         //     'header' => ['Name', 'Email'],
//         //     'rows'   => $userData
//         // ]);
//         $response = Http::post($exportUrl, $payload);

//         $fileUrl = $response->json('url');

//         $file = Http::get($fileUrl);

// Storage::disk('report-exports')->put(
//     'wa_' . now()->format('Ymd_His') . '.xlsx',
//     $file->body()
// );        
    }

    /**
     * Execute the job.
     */
    public function handle1(WorkActivityReportExporter $exporter): void
    {
        $user = User::find($this->userId);
        $export = $exporter->generate($this->filters, $this->fileName, $this->userId);

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
