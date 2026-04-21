<?php

namespace App\Jobs\Snapshots;

use App\Services\Snapshots\WorkTaskSubjectSnapshotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncWorkTaskSubjectSnapshotChunkJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public array $workTaskIds
    ) {}

    public function handle(): void
    {
        app(WorkTaskSubjectSnapshotService::class, [
            'workTaskIds' => $this->workTaskIds
        ])->handle();
    }

}
