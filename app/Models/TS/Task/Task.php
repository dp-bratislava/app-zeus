<?php

namespace App\Models\TS\Task;

use App\Models\TS\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'ticket_id',
        'status_id',
        'task_template_id',
        'note'
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
        ];
    } 

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, "ticket_id");
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, "status_id");
    }    

    public function template(): BelongsTo
    {
        return $this->belongsTo(TaskTemplate::class, "task_template_id");
    }        

    public function activities(): HasMany
    {
        return $this->hasMany(TaskActivity::class, "task_id");
    }         
}
