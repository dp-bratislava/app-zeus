<?php

namespace Dpb\Modules\Tasks\Resolvers;

use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;

class TaskItemGroupSubjectResolver
{
    public static function resolve(Model $model): Model
    {
        return match (true) {
            $model instanceof Vehicle => $model->model,
            default => __('Unknown subject'),
        };
    }
}
