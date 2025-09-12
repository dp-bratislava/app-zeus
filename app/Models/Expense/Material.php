<?php

namespace App\Models\Expense;

use App\Models\TS\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Material extends Model implements HasMedia
{
    use SoftDeletes;
    use InteractsWithMedia;

    protected $table = 'app_expenses_materials';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'code',
        'title',
        'description',
        'price',
        'vat',
        // 'attachments',
    ];

    protected $casts = [
        'date' => 'date',
        'attachments' => 'array',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('attachments')
            ->useDisk('public');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, "ticket_id");
    }   
}
