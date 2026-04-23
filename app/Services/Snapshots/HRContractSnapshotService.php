<?php

namespace App\Services\Snapshots;

use App\Registries\Snapshots\HRContractSnapshotSQLRegistry;
use Illuminate\Support\Facades\DB;

class HRContractSnapshotService
{
    public function __construct(
        public HRContractSnapshotSQLRegistry $sqlRegistry,
    ) {}

    public function handle(array $contractIds): void
    {
        // insert raw data
        $this->createTemporaryTables();

        DB::table('tmp_contract_ids')->insert(
            array_map(fn($id) => ['id' => $id], $contractIds)
        );

        DB::statement($this->sqlRegistry->build());

        $this->dropTemporaryTables();
    }

    protected function createTemporaryTables()
    {
        DB::statement("CREATE TEMPORARY TABLE tmp_contract_ids (id BIGINT PRIMARY KEY)");
    }

    protected function dropTemporaryTables()
    {
        DB::statement("DROP TEMPORARY TABLE tmp_contract_ids");
    }
}
