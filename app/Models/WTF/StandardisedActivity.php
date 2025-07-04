<?php

namespace App\Models\WTF;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandardisedActivity extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_standardised_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'task_id',
        'template_id',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, "task_id");
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(StandardisedActivityTemplate::class, "template_id");
    }
}
