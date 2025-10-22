<?php

namespace App\Models\Datahub;

use Dpb\DatahubSync\Models\EmployeeContract as Model;
use Illuminate\Database\Eloquent\Builder;

class EmployeeContract extends Model
{
    // Add custom properties and relationships here

    /**
     * riadiaci pracovnik
     * @return void
     */
    public function scopeManagers(Builder $query) {
        $query->whereHas('circuit', function($q) {
            $q->whereIn('code', [
                'TR'
            ]);
        });         
    }

    /**
     * robotnicke profesie
     * @return void
     */
    public function scopeWorkers(Builder $query) {
        $query->whereHas('circuit', function($q) {
            $q->whereIn('code', [
                'UA', 'UE', 'UT', 'UO',
                'OA', 'OE', 'OT', 'OR',
                'VM'
            ]);
        }); 
    }

    /**
     * Scope employee contracts by department
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $department
     * @return void
     */
    public function scopeByDepartment(Builder $query, string|array $department) 
    {
        // cast input to array
        $department = is_array($department) ? $department : [$department];

        $query->whereHas('department', function($q) use ($department) {
            $q->whereIn('code', $department);
        }); 
    }    
}