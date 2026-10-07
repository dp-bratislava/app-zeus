<?php

namespace Dpb\Modules\Tasks\TaskBatches\Resolvers;

use Dpb\WorkTimeFund\Models\Category;

class OperationCategoryResolver
{
    public function __construct(
        private Category $categoryModel,
    ) {}

    public function resolve(int $vehicleModelId): ?int
    {
        // @todo
        return 20;
    }
}
