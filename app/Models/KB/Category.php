<?php

namespace App\Models\KB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    
    protected $table = "dpb_kb_categories";

    protected $fillable = [
        "title",
        "slug",
        "parent_id",
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'category_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    } 
}
