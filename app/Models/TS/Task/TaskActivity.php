<?php

namespace App\Models\TS\Task;

use App\Models\Datahub\EmployeeContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskActivity extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_task_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_id',
        'employee_contract_id',
        'date',
        'time_from',
        'time_to',
        'note',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'time_from' => 'datetime',
            'time_to' => 'datetime',
        ];
    } 

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, "task_id");
    }

    public function employeeContract(): BelongsTo
    {
        return $this->belongsTo(EmployeeContract::class, "employee_contract_id");
    }    
}
