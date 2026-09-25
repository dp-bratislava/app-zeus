<?php

namespace Dpb\Modules\Tasks\Repositories;

use Dpb\Package\Fleet\Models\VehicleModel;
use Illuminate\Support\Facades\DB;

final class OperationCategoryRepository
{
    public function __construct(
        private DB $dbHdl,
    ) {}

    /**
     * @todo
     *
     * @param  mixed  $taskItemGroup
     * @param  mixed  $vehicleModel
     * @return void
     */
    public function getCategoryIdForTaskItem($taskItemGroup, $vehicleModelId): ?int
    {
        return $this->dbHdl->table('dpb_worktimefund_mm_category_relations')
            ->where('related_id', '=', $vehicleModelId)
            ->where('related_type', '=', VehicleModel::class)
            ->pluck('category_id')
            ->first()
            ?->category_id;
    }
}
