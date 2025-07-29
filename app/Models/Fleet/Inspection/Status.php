<?php

namespace App\Models\Fleet\Inspection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_fleet_inspection_statuses';
}
