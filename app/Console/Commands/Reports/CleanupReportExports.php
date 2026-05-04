<?php

namespace App\Console\Commands\Reports;

use App\Models\Reports\Export;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupReportExports extends Command
{
    protected $signature = 'report:exports:cleanup';
    protected $description = 'Delete all old report export files from storage and from DB';

    public function handle()
    {
        $exports = Export::where('created_at', '<', now()->subDay())->get();

        foreach ($exports as $export) {
            // delete file from storage
            $path = $export->file_name . ".xlsx";

            Storage::disk($export->file_disk)
                ->delete($path);

            // delete export record from database
            $export->delete();
        }

        $this->info('Old exports cleaned up.');
    }
}
