<?php

namespace App\Jobs\Reports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

/**
 * @TODO
 */
class CDCSyncWorkActivityReportChunkJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public array $ids,
        public string $mode // update | delete
    ) {}

    public function handle(): void
    {
        if ($this->mode === 'delete') {
            $this->handleDeletes();
            return;
        }

        // $this->handleUpserts();
    }

    private function handleDeletes(): void
    {
        DB::table('mvw_work_activity_report')
            ->whereIn('activityrecord_id', $this->ids)
            ->update([
                'source_deleted_at' => now()
            ]);
    }

    // private function handleUpserts(): void
    // {
    //     $rows = DB::table('dpb_worktimefund_model_activityrecord as ar')
    //         ->whereIn('ar.id', $this->ids)
    //         ->leftJoin(...)
    //         ->select(...)
    //         ->get();

    //     foreach ($rows as $row) {
    //         DB::table('mvw_work_activity_report')->updateOrInsert(
    //             ['activityrecord_id' => $row->id],
    //             (array) $row
    //         );
    //     }
    // }
}
