<?php

namespace App\Jobs\Snapshots;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

/**
 * Orchestrator job to queue actual updates in chunks
 */
class SyncHRContractSnapshotJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(): void
    {
        $state = DB::table('report_sync_state')
            ->where('report_name', 'mvw_hr_contract_snapshots')
            ->first();

        $lastSync = $state->last_synced_at ?? '1970-01-01 00:00:00';

        $ids = DB::table('datahub_employee_contracts')
            ->where(function ($q) use ($lastSync) {
                $q->where('updated_at', '>', $lastSync)
                    ->orWhere('deleted_at', '>', $lastSync);
            })
            ->whereNotNull([
                'datahub_department_id',
                'datahub_profession_id',
                'datahub_contract_type_id',
                'circuit_id'
            ])
            ->orderBy('id')
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        $chunks = $ids->chunk(1000);
        foreach ($chunks as $chunk) {
            SyncHRContractSnapshotChunkJob::dispatch(
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
