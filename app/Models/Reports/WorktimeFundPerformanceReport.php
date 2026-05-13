<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Model;

class WorktimeFundPerformanceReport extends Model
{
    public $timestamps = false;
    protected $table = 'dpb_worktimefund_model_activityrecord';
}
