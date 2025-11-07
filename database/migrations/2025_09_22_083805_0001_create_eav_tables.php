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
        $tablePrefix = config('pkg-eav.table_prefix');

        // attribute groups
        Schema::create($tablePrefix . 'attribute_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code')
                ->nullable()
                ->comment('')
                ->unique();
            $table->string('title')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // attribute types
        Schema::create($tablePrefix . 'attribute_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')
                ->nullable()
                ->comment('')
                ->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        // attribute sets
        Schema::create($tablePrefix . 'attribute_sets', function (Blueprint $table) {
            $table->id();
            $table->string('code')
                ->nullable()
                ->comment('');
            $table->string('title')
                ->nullable()
                ->comment('');
            $table->timestamps();
            $table->softDeletes();
        });

        // attributes
        Schema::create($tablePrefix . 'attributes', function (Blueprint $table) use ($tablePrefix) {
            $table->id();
            $table->string('code')
                ->nullable()
                ->comment('');                
            $table->string('title')
                ->nullable()
                ->comment('');
            $table->foreignId('group_id')
                ->nullable()
                ->comment('')
                ->constrained($tablePrefix . 'attribute_groups', 'id');
            $table->foreignId('type_id')
                ->nullable()
                ->comment('')
                ->constrained($tablePrefix . 'attribute_types', 'id');
            $table->timestamps();
            $table->softDeletes();

            // set unique 
            $table->unique(['code', 'group_id'], 'unq_attribute_code');
        });

        // attribute set attributes
        Schema::create($tablePrefix . 'attribute_set_attributes', function (Blueprint $table) use ($tablePrefix) {
            $table->id();

            $table->foreignId('set_id')
                ->nullable()
                ->comment('')
                ->constrained($tablePrefix . 'attribute_sets', 'id');
            $table->foreignId('attribute_id')
                ->nullable()
                ->comment('')
                ->constrained($tablePrefix . 'attributes', 'id');

            $table->timestamps();
            $table->softDeletes();
        });  

        // attribute set entities
        Schema::create($tablePrefix . 'attribute_set_entities', function (Blueprint $table) use ($tablePrefix) {
            $table->id();

            $table->foreignId('set_id')
                ->nullable()
                ->comment('')
                ->constrained($tablePrefix . 'attribute_sets', 'id');
            $table->string('entity_type');

            $table->timestamps();
            $table->softDeletes();
        });  

        // attribute values
        Schema::create($tablePrefix . 'attribute_values', function (Blueprint $table) use ($tablePrefix) {
            $table->id();

            $table->foreignId('attribute_id')
                ->nullable()
                ->comment('')
                ->constrained($tablePrefix . 'attributes', 'id');

            $table->unsignedBigInteger('entity_id')->nullable(false);
            $table->string('entity_type')->nullable(false);

            $table->integer('value_int')->nullable();
            $table->decimal('value_decimal')->nullable();
            $table->string('value_string')->nullable();
            $table->boolean('value_bool')->nullable();
            $table->date('value_date')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-eav.table_prefix');

        Schema::dropIfExists($tablePrefix . 'attribute_set_entities');
        Schema::dropIfExists($tablePrefix . 'attribute_set_attributes');
        Schema::dropIfExists($tablePrefix . 'attribute_values');
        Schema::dropIfExists($tablePrefix . 'attributes');
        Schema::dropIfExists($tablePrefix . 'attribute_sets');
        Schema::dropIfExists($tablePrefix . 'attribute_groups');
        Schema::dropIfExists($tablePrefix . 'attribute_types');
    }
};
