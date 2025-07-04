<?php

namespace App\Models\WTF;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityStatus extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_activity_statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'title',
    ];

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, "status_id");
    }
}
