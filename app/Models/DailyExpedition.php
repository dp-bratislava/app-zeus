<?php

namespace App\Models;

use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyExpedition extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'vehicle_id',
        'state',
        'service',
        'note',
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'daily_expeditions';
    }    

    public function vehicle(): BelongsTo 
    {
        return $this->belongsTo(Vehicle::class);
    }     
}
