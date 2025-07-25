<?php

namespace Database\Seeders\TS;

use App\Models\TS\Task\TaskStatus;
use App\Models\TS\Task\TemplateGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskEnumSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('dpb_ts_task_statuses')->truncate();
        DB::table('dpb_ts_task_template_groups')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // task statuses
        $taskStatuses = [
            ['code' => 'new', 'title' => 'Nová'],
            ['code' => 'in_progress', 'title' => 'V riešení'],
            ['code' => 'closed', 'title' => 'Uzavretá'],
        ];

        foreach ($taskStatuses as $taskStatus) {
            TaskStatus::create($taskStatus);
        }

        // task template groups
        $taskTemplateGroups = [
            ['title' => 'Katalogizované úlohy'],
            // ['title' => 'Normované úlohy'],
            ['title' => 'Vlastné úlohy'],
        ];

        foreach ($taskTemplateGroups as $taskTemplateGroup) {
            TemplateGroup::create($taskTemplateGroup);
        }        
    }
}
