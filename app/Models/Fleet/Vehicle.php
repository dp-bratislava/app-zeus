<?php

namespace App\Models\Fleet;

use App\Models\WTF\Task;
use App\Models\WTF\TaskSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_fleet_vehicles';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'licence_plate',
        'end_of_warranty',
        'model_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'end_of_warranty' => 'date',
        ];
    } 

    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, "model_id");
    }    
    // public function tasks(): MorphToMany
    // {
    //     return $this->morphToMany(
    //         Task::class, 
    //         'subject',
    //         'dpb_wtf_task_subjects',
    //         'task_id',
    //         'subject_id'
    //     );
    // }
}
