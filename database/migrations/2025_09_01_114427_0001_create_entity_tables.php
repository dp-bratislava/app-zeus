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
        $tablePrefix = config('pkg-entity-manager.table_prefix');

        // generic entity groups
        Schema::create($tablePrefix . 'entity_groups', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of entity groups. E.g. buildings, IT, vehicles');
            $table->id();

            $table->string('code')->nullable(false);
            $table->string('title')->nullable(false);
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // generic entity types
        Schema::create($tablePrefix . 'entity_types', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of entity types. Building, Office, Areal, ...');
            $table->id();

            $table->string('code')->nullable(false);
            $table->string('title')->nullable(false);
            $table->string('description')->nullable();
            $table->foreignId('group_id')
                ->nullable()
                ->comment('Type belongs to entity group.')
                ->constrained($tablePrefix . 'entity_groups', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        // generic entities
        Schema::create($tablePrefix . 'entities', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of entities for generic pupose lists and as subjects for tickets or activities etc.');
            $table->id();
            $table->foreignId('type_id')
                ->nullable()
                ->constrained($tablePrefix . 'entity_types', 'id');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // entity relations
        Schema::create($tablePrefix . 'entity_relations', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of entity relation between owner and target. E.g. subjects for tickets or activities etc.');
            $table->id();
            $table->string('owner_type', 50)
                ->nullable(false);
            $table->unsignedBigInteger('owner_id')
                ->nullable(false)
                ->comment('FK into respective owner entity table. Not enforced by database. E.g.project, ticket, task, ...');
            $table->string('target_type', 50)
                ->nullable(false);
            $table->unsignedBigInteger('target_id')
                ->nullable(false)
                ->comment('FK into respective target entity table. Not enforced by database. E.g. vehicle, building, ...');
            $table->string('relation_type', 50)
                ->nullable()
                ->comment('Optional description of relation type, E.g. assigned, uses, location, ...');
            $table->timestamps();
            $table->softDeletes();

            // unique constraints
            $table->unique(
                ['owner_type', 'owner_id', 'target_type', 'target_id'],
                'unq_em_owner_target'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-entity-manager.table_prefix');

        Schema::dropIfExists($tablePrefix . 'entity_relations');
        Schema::dropIfExists($tablePrefix . 'entitys');
        Schema::dropIfExists($tablePrefix . 'entity_types');
        Schema::dropIfExists($tablePrefix . 'entity_groups');
    }
};
