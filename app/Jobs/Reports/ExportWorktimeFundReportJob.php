<?php

namespace App\Jobs\Reports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportWorktimeFundReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $fileName;
    protected $userId;

    public function __construct($filters, $fileName, $userId)
    {
        $this->filters = $filters;
        $this->fileName = $fileName;
        $this->userId = $userId;
    }

    public function handle()
    {
        // TODO: Implement export logic
        // This should generate Excel file with worktime fund performance data
        // and store it in the user's storage
    }
}
