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
        $tablePrefix = config('database.table_prefix');
        $currencyTable = config('database.tables.currency');
        $unitTable = config('database.tables.units');

        // work interval
        Schema::create($tablePrefix . 'unit_rates', function (Blueprint $table) use ($currencyTable, $unitTable) {
            $table->id();

            $table->date('date_from')->nullable()->comment('This unit rate for this subject is valid from this date.');
            $table->date('date_to')->nullable()->comment('This unit rate for this subject is valid to this date.');
            $table->decimal('unit_price')
                ->nullable()
                ->comment('Unit price for specified unit.');;
            $table->foreignId('unit_id')
                ->nullable()
                ->comment('Rate is for this type of unit.')
                ->constrained($unitTable, 'id');
            $table->foreignId('currency_id')
                ->nullable()
                ->comment('Rate curerncy')
                ->constrained($currencyTable, 'id');
            $table->morphs('rateable');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-finance.table_prefix');

        Schema::dropIfExists($tablePrefix . 'unit_rates');
    }
};
