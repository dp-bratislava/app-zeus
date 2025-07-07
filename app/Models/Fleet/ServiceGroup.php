<?php

namespace App\Models\Fleet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceGroup extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_fleet_service_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'short_title',
    ];
}
