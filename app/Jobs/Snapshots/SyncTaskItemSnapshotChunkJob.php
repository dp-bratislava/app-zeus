<?php

namespace App\Jobs\Snapshots;

use App\Services\Snapshots\TaskItemSnapshotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncTaskItemSnapshotChunkJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public array $taskItemIds
    ) {}

    public function handle(): void
    {
        app(TaskItemSnapshotService::class)->handle($this->taskItemIds);
    }
}
