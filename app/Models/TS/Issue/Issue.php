<?php

namespace App\Models\TS\Issue;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
{
    use SoftDeletes;

    protected $table = 'dpb_ts_issues';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'type_id',
        'subject_id',
        'status_id',
        'subject_type',
        'description'
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

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, "type_id");
    }
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, "status_id");
    }
    // public function status(): Belon
    // {
    //     return $this->hasOne(Status::class, "status_id", "id", "");
    // }  
}
