<?php

namespace Dpb\Modules\Tasks\Eligibility;

use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;

final class VehicleRuleEvaluator implements SubjectRuleEvaluator
{
    public function supports(Model $subject): bool
    {
        return $subject instanceof Vehicle;
    }

    public function matches(
        Model $subject,
        array $conditions,
    ): bool {
        if (! $this->supports($subject)) {
            return false;
        }

        $vehicle = $subject;
        $model = $vehicle->model;

        if (! $model) {
            return false;
        }

        if (isset($conditions['length'])) {
            $length = $model->length;
            $range = $conditions['length'];

            if (isset($range['gte']) && $length < $range['gte']) {
                return false;
            }

            if (isset($range['lt']) && $length >= $range['lt']) {
                return false;
            }

            if (isset($range['lte']) && $length > $range['lte']) {
                return false;
            }

            if (isset($range['gt']) && $length <= $range['gt']) {
                return false;
            }
        }

        // if (isset($conditions['vehicle_type'])) {
        //     $allowedTypes = $conditions['vehicle_type']['in'] ?? [];

        //     if (! in_array($model->type->code, $allowedTypes, true)) {
        //         return false;
        //     }
        // }
logger('rule passed');
        return true;
    }
}