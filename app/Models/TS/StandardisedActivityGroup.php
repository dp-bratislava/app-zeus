<?php

namespace App\Models\TS;

use App\Models\Datahub\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandardisedActivityGroup extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_standardised_activity_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'title',
        'description',
        'department_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(StandardisedActivityGroup::class, "parent_id");
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, "department_id");
    }

    public function activities(): HasMany
    {
        return $this->hasMany(StandardisedActivity::class, "group_id");
    }   
}
