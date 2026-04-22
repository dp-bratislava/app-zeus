<?php

namespace App\Jobs\Reports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrator job to queue actual updates in chunks
 */
class SyncWorkActivityReportJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(): void
    {
        $state = DB::table('report_sync_state')
            ->where('report_name', 'mvw_work_activity_report')
            ->first();

        $lastSync = $state->last_synced_at ?? '1970-01-01 00:00:00';

        // STEP 1: get ID range to process
        $ids = DB::table('dpb_worktimefund_model_activityrecord')
            ->where('updated_at', '>', $lastSync)
            ->orWhere('deleted_at', '>', $lastSync)
            ->orderBy('id')
            // ->limit(10)
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        // STEP 2: chunk IDs
        // $chunks = $ids->chunk(1000);
        $chunks = $ids->chunk(10000);
        foreach ($chunks as $chunk) {
            SyncWorkActivityReportChunkJob::dispatch(
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
