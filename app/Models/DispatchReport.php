<?php

namespace App\Models;

use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DispatchReport extends Model
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
        'description',
        'author_id',
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'dispatch_reports';
    }    

    public function author(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    } 

    public function vehicle(): BelongsTo 
    {
        return $this->belongsTo(Vehicle::class);
    }     

    // public function perex(): string
    // {
    //     return 
    // }     

}
