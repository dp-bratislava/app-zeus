<?php

namespace App\Models\WTF;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityGroup extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_activity_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
        'description',
        'parent_id',
        'department_id',
    ];

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, "group_id");
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ActivityGroup::class, "parent_id");
    }
      
}
