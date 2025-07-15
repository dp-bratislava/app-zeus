<?php

namespace App\Models\KB;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $table = "dpb_kb_articles";

    protected $fillable = [
        "title",
        "content",
        "category_id",
        "title",
        "author_id",
        "is_published"
    ];

    public function attachments(): MorphMany
    {
        return $this->media();
    }
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, "category_id");
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, "author_id");
    }

    public function tags(): BelongsToMany
    {
        return $this
            ->belongsToMany(Tag::class, 'dpb_kb_article_tag', 'article_id', 'tag_id');
    }

}