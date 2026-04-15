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
class CDCSyncWorkActivityReportJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(): void
    {
        $state = DB::table('report_sync_state')
            ->where('report_name', 'mvw_work_activity_report')
            ->first();

        $lastSync = $state->last_synced_at ?? '1970-01-01 00:00:00';

        // update stream
        DB::table('dpb_worktimefund_model_activityrecord')
            ->where('updated_at', '>', $lastSync)
            ->orderBy('id')
            ->select('id')
            ->chunkById(2000, function ($rows) {
                SyncWorkActivityReportChunkJob::dispatch(
                    $rows->pluck('id')->toArray(),
                    'update'
                );
            });

        // delete stream
        DB::table('dpb_worktimefund_model_activityrecord')
            ->where('deleted_at', '>', $lastSync)
            ->orderBy('id')
            ->select('id')
            ->chunkById(2000, function ($rows) {
                SyncWorkActivityReportChunkJob::dispatch(
                    $rows->pluck('id')->toArray(),
                    'delete'
                );
            });

        DB::table('report_sync_state')
            ->where('id', $state->id)
            ->update([
                'last_synced_at' => now(),
            ]);
    }
}
