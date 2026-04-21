<?php

namespace App\Services\Snapshots;

use Illuminate\Support\Facades\DB;

class TaskSubjectSnapshotService
{


    // public function sync(BatchData $batch): array
    // {
    //     $resolved = $this->resolver->resolveMany($batch->subjects);

    //     DB::table('mvw_task_subject_snapshots')->upsert(
    //         $resolved,
    //         ['subject_type', 'subject_id'],
    //         ['label', 'code', 'meta', 'updated_at']
    //     );

    //     return $this->mapIds($resolved);
    // }

    // private function mapIds($resolved)  {
    //     return [];
    // }
}