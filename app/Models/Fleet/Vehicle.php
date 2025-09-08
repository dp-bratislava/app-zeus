<?php

namespace App\Models\Fleet;

use App\Models\TS\Ticket;
use Dpb\Packages\Vehicles\Models\Vehicle as BaseVehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends BaseVehicle
{
    use SoftDeletes;

    public function tickets(): MorphMany
    {
        return $this->morphMany(
            Ticket::class, 
            'subject',
            'subject_type',
            'subject_id',
            'id',
        );
    }

    // public function tasks(): MorphToMany
    // {
    //     return $this->morphToMany(
    //         Task::class, 
    //         'subject',
    //         'dpb_wtf_task_subjects',
    //         'subject_id',
    //         'task_id',
    //     );
    // }
}
