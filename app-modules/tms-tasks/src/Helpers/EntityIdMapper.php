<?php

namespace Dpb\Modules\Tasks\Helpers;

use Dpb\Modules\Tasks\DTO\EntityIdMap;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Builds EntityIdMap containing id to UUID mapping 
 * for specified model and list of UUIDs
 */
class EntityIdMapper
{
    /**
     * @param class-string<Model> $model
     */
    public function map(
        string $model,
        Collection $uuids,
    ): EntityIdMap {
        return new EntityIdMap(
            $model::query()
                ->whereIn('uuid', $uuids)
                ->pluck('id', 'uuid')
        );
    }
}
