<?php

namespace App\Models\Fleet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportGroup extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_fleet_transport_groups';

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
