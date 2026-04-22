<?php

namespace App\Resolvers\Snapshots;

use Illuminate\Database\Eloquent\Model;

class TaskSubjectResolver
{
    protected array $map = [
        'vehicleDpb\WorkTimeFund\Models\Maintainables\Vehicle' => 'resolveVehicle',
        'tableDpb\WorkTimeFund\Models\Maintainables\Table' => 'resolveTable',
        'custom' => 'resolveCustom',
    ];

    public function resolve(Model $model): ?string
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

    protected function resolveVehicle($model): string
    {
        return $model->getTitle();
    }

    protected function resolveTable($model): string
    {
        return $model->getTitle();
    }
}
