<?php

namespace App\Models\WTF;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskMaterial extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_wtf_task_materials';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_id',
        'title',
        'unit_price',
        'vat',
        'quantity',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, "task_id");
    }
}
