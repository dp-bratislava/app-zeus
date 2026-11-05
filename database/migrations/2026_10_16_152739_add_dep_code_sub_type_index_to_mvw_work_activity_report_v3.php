
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mvw_work_activity_report_v3', function (Blueprint $table) {
            $table->index(
                ['department_code', 'subject_type'],
                'dep_code_subject_type_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('mvw_work_activity_report_v3', function (Blueprint $table) {
            $table->dropIndex('dep_code_subject_type_index');
        });
    }
};
