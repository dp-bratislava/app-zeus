<?php

namespace App\Services\Reports;

use App\Registries\Reports\WorkActivityReportSQLRegistry;
use App\Resolvers\Reports\WorkActivityReportResolver;
use Illuminate\Support\Facades\DB;

class WorkActivityReportService
{
    public function __construct(
        public WorkActivityReportSQLRegistry $sqlRegistry,
        public WorkActivityReportResolver $polymorphicsResolver
    ) {}

    public function handle(array $activityIds): void
    {
        // insert raw data
        $this->createTemporaryTables();

        DB::table('tmp_activity_record_ids')->insert(
            array_map(fn($id) => ['id' => $id], $activityIds)
        );

        // dd($this->sqlRegistry->build());

        DB::statement($this->sqlRegistry->build());

        $this->dropTemporaryTables();

        // resolve polymorphic data
        collect($activityIds)
            ->chunk(2000)
            ->each(function ($chunk) {
                $this->resolvePolymorphics($chunk);
            });
    }

    protected function createTemporaryTables()
    {
        DB::statement("CREATE TEMPORARY TABLE tmp_activity_record_ids (id BIGINT PRIMARY KEY)");
    }

    protected function dropTemporaryTables()
    {
        DB::statement("DROP TEMPORARY TABLE tmp_activity_record_ids");
    }

    protected function resolvePolymorphics($taskItemIds)
    {
        $context = DB::table(DB::raw("({$this->sqlRegistry->polymorphicContext($taskItemIds->toArray())}) as ctx"))
            ->get();

        if ($context->isEmpty()) {
            return;
        }

        $arRelations = $this->polymorphicsResolver->batchResolve($context);

        // dd($taskSnapshots);
        DB::table('mvw_work_activity_report')->upsert(
            $arRelations,
            ['activity_id'],
            [
                'activity_type',
                // 'activity_is_tolerated',
            ]
        );
    }
}
