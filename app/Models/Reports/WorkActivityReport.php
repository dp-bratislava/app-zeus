<?php

namespace App\Models\Reports;

use App\Models\Snapshots\WorkTaskSubject;
use Illuminate\Database\Eloquent\Model;

class WorkActivityReport extends Model
{
    protected $table = 'mvw_work_activity_report';

    public $timestamps = false;

    protected $guarded = [];

    public function taskSubjects()
    {
        return $this->hasMany(
            WorkTaskSubject::class,
            'wtf_task_id', // Foreign key on TaskSubject table
            'wtf_task_id'  // Local key on Activity table
        );
    }
}
