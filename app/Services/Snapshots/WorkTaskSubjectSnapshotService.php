<?php

namespace App\Services\Snapshots;

use App\Registries\Snapshots\WorkTaskSubjectSnapshotSQLRegistry;
use App\Resolvers\Snapshots\WorkTaskSubjectResolver;
use Dpb\WorkTimeFund\Models\Task;
use Illuminate\Support\Facades\DB;

class WorkTaskSubjectSnapshotService
{
    public function __construct(
        public WorkTaskSubjectSnapshotSQLRegistry $sqlRegistry,
    ) {}

    public function handle(array $taskIds): void
    {
        $this->createTemporaryTables();

        DB::table('tmp_wtf_task_ids')->insert(
            array_map(fn($id) => ['id' => $id], $taskIds)
        );

        // dd($this->sqlRegistry->build());
        DB::statement($this->sqlRegistry->build());

        $this->dropTemporaryTables();
    }

    protected function createTemporaryTables()
    {
        DB::statement("CREATE TEMPORARY TABLE tmp_wtf_task_ids (id BIGINT PRIMARY KEY)");
    }

    protected function dropTemporaryTables()
    {
        DB::statement("DROP TEMPORARY TABLE tmp_wtf_task_ids");
    }    
}
