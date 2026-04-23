<?php

namespace App\Services\Snapshots;

use App\Registries\Snapshots\TaskItemSnapshotSQLRegistry;
use App\Resolvers\Snapshots\TaskItemSnapshotResolver;
use Illuminate\Support\Facades\DB;

class TaskItemSnapshotService
{
    public function __construct(
        public TaskItemSnapshotSQLRegistry $sqlRegistry,
        public TaskItemSnapshotResolver $polymorphicsResolver
    ) {}

    public function handle(array $taskItemIds): void
    {
        // insert raw data
        $this->createTemporaryTables();

        DB::table('tmp_task_item_ids')->insert(
            array_map(fn($id) => ['id' => $id], $taskItemIds)
        );

        DB::statement($this->sqlRegistry->build());

        $this->dropTemporaryTables();

        // resolve polymorphic data
        collect($taskItemIds)
            ->chunk(2000)
            ->each(function ($chunk) {
                $this->resolvePolymorphics($chunk);
            });
    }

    protected function createTemporaryTables()
    {
        DB::statement("CREATE TEMPORARY TABLE tmp_task_item_ids (id BIGINT PRIMARY KEY)");
    }

    protected function dropTemporaryTables()
    {
        DB::statement("DROP TEMPORARY TABLE tmp_task_item_ids");
    }

    protected function resolvePolymorphics($taskItemIds)
    {
        $context = DB::table(DB::raw("({$this->sqlRegistry->polymorphicContext($taskItemIds->toArray())}) as ctx"))
            ->get();

        if ($context->isEmpty()) {
            return;
        }

        $tisRelations = $this->polymorphicsResolver->batchResolve($context);

        DB::table('mvw_task_item_snapshots')->upsert(
            $tisRelations,
            ['task_item_id'],
            [
                'task_assigned_to_type',
                'task_assigned_to_label',
                'task_requested_for_type',
                'task_requested_for_label',
                'task_subject_type',
                'task_subject_label',
                'task_item_assigned_to_type',
                'task_item_assigned_to_label',
            ]
        );
    }
}
