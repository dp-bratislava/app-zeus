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

    public function __construct(
        public ?string $from = null,
        public ?string $to = null,
        public bool $all = false,
        public bool $force = false,
    ) {}

    public function handle(): void
    {
        $state = DB::table('report_sync_state')
            ->where('report_name', 'mvw_hr_contract_snapshots')
            ->first();

        $query = DB::table('datahub_employee_contracts')
            ->whereNotNull([
                'datahub_department_id',
                'datahub_profession_id',
                'datahub_contract_type_id',
                'circuit_id'
            ]);

        // FULL SYNC
        if ($this->all) {
            // no date filter
        }

        // CUSTOM RANGE
        elseif ($this->from || $this->to) {
            $query->where(function ($q) {
                if ($this->from) {
                    $q->where('updated_at', '>=', $this->from)
                        ->orWhere('deleted_at', '>=', $this->from);
                }

                if ($this->to) {
                    $q->where('updated_at', '<=', $this->to)
                        ->orWhere('deleted_at', '<=', $this->to);
                }
            });
        }

        // INCREMENTAL (default)
        else {
            $lastSync = $state->last_synced_at ?? '1970-01-01 00:00:00';

            $query->where(function ($q) use ($lastSync) {
                $q->where('updated_at', '>', $lastSync)
                    ->orWhere('deleted_at', '>', $lastSync);
            });
        }

        $ids = $query->orderBy('id')->pluck('id');

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

    public function handle1(): void
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
