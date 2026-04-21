<?php

namespace App\Services\Snapshots;

use App\Resolvers\Snapshots\HRContractResolver;
use Dpb\DatahubSync\Models\EmployeeContract;
use Illuminate\Support\Facades\DB;

class HRContractSnapshotService
{
    public function __construct(
        public array $contractIds
    ) {}

    public function handle(): void
    {
        $contracts = EmployeeContract::whereIn('id', $this->contractIds)
            ->with([
                'employee:id,hash,first_name,last_name,gender',
                'department:id,code,title',
                'profession:id,code,title',
                'circuit',
                'type',
            ])
            ->get()
            ->keyBy('id');

        $contractResolver = app(HRContractResolver::class);
        $contractSnapshots = $contractResolver->batchResolve($contracts);

        DB::table('mvw_hr_contract_snapshots')->upsert(
            $contractSnapshots,
            ['contract_id'],
            // ['task_item_date', 'task_item_title', 'create_at', 'updated_at']
        );
    }
}
