<?php

namespace App\Resolvers\Snapshots;

use Illuminate\Database\Eloquent\Model;

class TaskItemAssignedToResolver
{
    protected array $map = [
        'maintenance-group' => 'resolveMaintenanceGroup',
        'department' => 'resolveDepartment',
    ];

    public function resolve(Model|null $model): string|null
    {
        if (!$model) {
            return null;
        }

        $class = ($model->getMorphClass());

        if (!isset($this->map[$class])) {
            return null; // or throw exception if strict
        }

        return $this->{$this->map[$class]}($model);
    }

    protected function resolveMaintenanceGroup($model): string|null
    {
        return $model->code;
    }

    protected function resolveDepartment($model): string|null
    {
        return $model->code;
    }
}
