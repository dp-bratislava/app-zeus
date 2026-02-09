<?php

namespace App\Models;

use Dpb\Package\Fleet\Models\VehicleType;
use Dpb\UserAdmin\Models\User as BaseUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends BaseUser
{
    use HasFactory;
    
    public function vehicleTypes(): array
    {
        if (isset($this->properties['fleet-vehicle-type-id'])) {
            return VehicleType::whereIn('id', ($this->properties['fleet-vehicle-type-id']))
                ->pluck('code', 'id')
                ->toArray();
        }

        return [];
    }


    /**
     * Get array of maintenance group ids set in user properties
     * 
     * @return array
     */
    public function getMaintenanceGroupIds(): array
    {
        if (isset($this->properties['fleet-maintenance-group-id'])) {
            return $this->properties['fleet-maintenance-group-id'];
        }

        return [];
    }  
    
    /**
     * Get array of vehicle type ids set in user properties
     * 
     * @return array
     */
    public function getVehicleTypeIds(): array
    {
        if (isset($this->properties['fleet-vehicle-type-id'])) {
            return $this->properties['fleet-vehicle-type-id'];
        }

        return [];
    }     
}