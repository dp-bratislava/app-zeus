<?php

namespace App\Models\KB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $table = "dpb_kb_tags";

    protected $fillable = [
        "title",
        "slug",
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(
            Article::class,
            'dpb_kb_article_tags',
            'tag_id',
            'article_id'
        );
    }
}
