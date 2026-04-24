<?php

namespace App\Jobs\Snapshots;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrator job to queue actual updates in chunks
 */
class SyncWorkTaskSubjectSnapshotJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(): void
    {
        $state = DB::table('report_sync_state')
            ->where('report_name', 'mvw_work_task_subject_snapshots')
            ->first();

        $lastSync = $state->last_synced_at ?? '1970-01-01 00:00:00';

        // STEP 1: get ID range to process
        $ids = DB::table('dpb_worktimefund_model_task')
            ->where('updated_at', '>', $lastSync)
            ->orderBy('id')
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        // STEP 2: chunk IDs
        $chunks = $ids->chunk(10000);
        foreach ($chunks as $chunk) {
            SyncWorkTaskSubjectSnapshotChunkJob::dispatch(
                $chunk->toArray()
            );
        }

        DB::table('report_sync_state')
            ->where('id', $state->id)
            ->update([
                'last_synced_at' => now(),
            ]);
    }
}
