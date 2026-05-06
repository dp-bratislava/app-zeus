<?php

namespace App\Models\Snapshots;

use App\Models\Reports\WorkActivityReport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkTaskSubject extends Model
{
    protected $table = 'mvw_work_task_subject_snapshots';

    public $timestamps = false;

    protected $guarded = [];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(
            WorkActivityReport::class,
            'wtf_task_id', // Foreign key on TaskSubject table
            'wtf_task_id'  // Local key on Activity table            
        );
    }
}
