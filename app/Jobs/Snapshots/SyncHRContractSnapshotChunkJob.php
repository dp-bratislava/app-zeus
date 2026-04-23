<?php

namespace App\Jobs\Snapshots;

use App\Services\Snapshots\HRContractSnapshotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncHRContractSnapshotChunkJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public array $contractIds
    ) {}

    public function handle(): void
    {
        app(HRContractSnapshotService::class)->handle($this->contractIds);
    }

}
