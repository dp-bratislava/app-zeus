<?php

namespace App\Models\State;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StateChange extends Model
{
    public $timestamps = false;    

    protected $table = 'model_state_changes';

    protected $fillable = [
        'model_type', 
        'model_id', 
        'from_state', 
        'to_state', 
        'user_id', 
        'changed_at',
        'source'
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
