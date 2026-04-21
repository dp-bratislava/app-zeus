<?php

namespace App\Services\Snapshots;

use App\Resolvers\Snapshots\WorkTaskSubjectResolver;
use Dpb\WorkTimeFund\Models\Task;
use Illuminate\Support\Facades\DB;

class WorkTaskSubjectSnapshotService
{
    public function __construct(
        public array $workTaskIds
    ) {}

    public function handle(): void
    {
        $workTaskSubjects = Task::whereIn('id', $this->workTaskIds)
            ->with(['maintainable', 'attributeOptions', 'attributeOptions.type'])
            ->get();

        //   dd($workTaskSubjects[0]) ;          
        //   exit;
        $wtsResolver = app(WorkTaskSubjectResolver::class);
        $taskSnapshots = $wtsResolver->batchResolve($workTaskSubjects);

        // dd($taskSnapshots);
        DB::table('mvw_work_task_subject_snapshots')->upsert(
            $taskSnapshots,
            ['subject_type', 'subject_id'],
            // ['task_item_date', 'task_item_title', 'create_at', 'updated_at']
        );
    }
}
