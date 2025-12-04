<?php

namespace App\Models;

use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Fleet\Models\VehicleModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class ActivityTemplatable extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'template_id',
        'templatable_id',
        'templatable_type',
    ];

    public function getTable()
    {
        return config('database.table_prefix') . 'activity_templatables';
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ActivityTemplate::class, 'template_id');
    }

    public function vehicleModels(): MorphMany
    {
        return $this->morphMany(
            VehicleModel::class,
            'templatable',            
        );
    }

    public function scopeByTemplatable(Builder $query, string $templateType, int|array $id)
    {
        $idList = Arr::wrap($id);

        $query->whereMorphedTo('templatable', $templateType)
            ->whereIn('templatable_id', $idList);
    }
}
