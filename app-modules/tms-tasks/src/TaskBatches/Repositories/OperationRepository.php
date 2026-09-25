<?php

namespace Dpb\Modules\Tasks\Repositories;

use Dpb\WorkTimeFund\Models\Operation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class OperationRepository
{
    public function __construct(
        private Operation $operationModel,
        private DB $dbHdl,
    ) {}

    public function getByCategoryId($categoryId): Collection
    {
        return $this->operationModel
            ->whereHas('parent', function ($q) use ($categoryId) {
                $q->where('id', '=', $categoryId);
            })
            ->get();
    }

    public function getPickerOptionsByCategoryId($categoryId): Collection
    {
        return $this->operationModel
            ->whereHas('parent', function ($q) use ($categoryId) {
                $q->where('id', '=', $categoryId);
            })
            ->pluck(
                DB::raw("CONCAT(title, ' [', duration DIV 60, ' min]')"),
                'id'
            );
    }

    /**
     * @todo
     *
     * @param  mixed  $taskItemGroup
     * @param  mixed  $vehicleModel
     * @return void
     */
    public function getOptionsForTaskItemForm($taskItemGroup, $vehicleModel, $search = null): Collection
    {
        // $this->operationModel
        //     ->whereHas('parent', function($q) {
        //        $q->
        //     })
        return $this->operationModel
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            })
            // by category of operations based on task item group
            // ->where()
            // by category of operations based on vehicle model
            //             ->when($taskItemGroup, function ($q) use ($taskItemGroup) {
            //             ->whereHas('parent', function($q) {
            // $q->
            //             })
            //         })
            ->pluck(
                $this->dbHdl::raw("CONCAT(title, ' [', duration DIV 60, ' min]')"),
                'id'
            );
    }
}
