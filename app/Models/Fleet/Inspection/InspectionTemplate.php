<?php

namespace App\Models\Fleet\Inspection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_fleet_inspection_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'is_one_time',
        'is_periodical',
        'note',
        'distance_interval',
        'distance_first_advance',
        'distance_second_advance',
        'time_interval',
        'time_first_advance',
        'time_second_advance',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_one_time' => 'boolean',
            'is_periodical' => 'boolean',
        ];
    } 
}
