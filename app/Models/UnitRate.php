<?php

namespace App\Models;

use Dpb\Packages\Utils\Models\Currency;
use Dpb\Packages\Utils\Models\MeasurementUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitRate extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date_from',
        'date_to',
        'unit_price',
        'unit_id',
        'currency_id',
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'unit_rates';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_from' => 'date',
            'date_to' => 'date',
        ];
    }

    public function getFormattedRateAttribute(): ?string
    {
        if (($this->unit_price != null) && ($this->has('unit'))) {
            return $this->unit_price . ' / ' . $this->unit?->code;
        }
        return null;
    } 

    public function currency(): BelongsTo 
    {
        return $this->belongsTo(Currency::class);
    } 

    public function unit(): BelongsTo 
    {
        return $this->belongsTo(MeasurementUnit::class);
    } 

    public function rateable(): MorphTo
    {
        return $this->morphTo();
    }    
}
