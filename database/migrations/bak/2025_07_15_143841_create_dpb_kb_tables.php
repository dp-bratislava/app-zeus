<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // kb article categories
        Schema::create('dpb_kb_categories', function (Blueprint $table) {
            // table description
            $table->comment('List of article categories.');

            // table data
            $table->id();
            $table->string('title')->nullable(false)->comment('Category title displayed to users.');
            $table->string('slug')->nullable(false)->comment('Unique slug for category used in code to specify category.');
            $table->foreignId('parent_id')
                ->nullable(true)
                ->comment('Parent category for hierarchical category structure.')
                ->constrained('dpb_kb_categories', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        // kb tags
        Schema::create('dpb_kb_tags', function (Blueprint $table) {
            // table description
            $table->comment('List of article tags');

            // table data
            $table->id();
            $table->string('title')->nullable(false)->comment('Tag title displayed to users.');
            $table->string('slug')->nullable(false)->unique()->comment('Unique slug for tag used in code to specify tag.');            
            $table->timestamps();
            $table->softDeletes();
        });

        // kb articles
        Schema::create('dpb_kb_articles', function (Blueprint $table) {
            // table description
            $table->comment('List of articles.');

            // table data
            $table->id();
            $table->string('title')->nullable(false)->comment('Article title displayed to users.');
            $table->string('slug')->nullable(true)->unique()->comment('Unique slug for article used in code to specify article.');
            $table->text('content')->nullable(true)->comment('Content of article.');
            $table->foreignId('category_id')
                ->nullable(false)
                ->comment('Category of the article.')
                ->constrained('dpb_kb_categories', 'id');
            $table->foreignId('author_id')
                ->nullable(false)
                ->comment('User that created the article.')
                ->constrained('users', 'id');
            $table->boolean('is_published')->nullable(false)->default(false)->comment('If article is published.');
            $table->timestamps();
            $table->softDeletes();
        });

        // kb article tag pivot
        Schema::create('dpb_kb_article_tag', function (Blueprint $table) {
            // table description            
            $table->comment('Pivot table for article tags.');

            // table data
            $table->id();
            $table->foreignId('article_id')
            ->nullable(false)
            ->constrained('dpb_kb_articles', 'id');
            $table->foreignId('tag_id')
                ->nullable(false)
                ->constrained('dpb_kb_tags', 'id');
            $table->timestamps();
            $table->softDeletes();

            // constraints
            $table->unique(['article_id', 'tag_id']);
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_kb_article_tag');
        Schema::dropIfExists('dpb_kb_articles');
        Schema::dropIfExists('dpb_kb_categories');
        Schema::dropIfExists('dpb_kb_tags');
    }
};
