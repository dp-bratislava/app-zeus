<?php

namespace App\Models;

use Dpb\Package\Fleet\Models\VehicleType;
use Dpb\UserAdmin\Models\User as BaseUser;

class User extends BaseUser
{
    public function vehicleTypes(): array
    {
        if (isset($this->properties['fleet-vehicle-type-id'])) {
            return VehicleType::whereIn('id', ($this->properties['fleet-vehicle-type-id']))
                ->pluck('code', 'id')
                ->toArray();
        }

        return [];
    }
}