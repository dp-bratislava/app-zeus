<?php

namespace Database\Seeders\WTF;

use App\Models\WTF\ActivityStatus;
use App\Models\WTF\ActivityType;
use App\Models\WTF\TaskPriority;
use App\Models\WTF\TaskStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnumSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('dpb_wtf_activity_statuses')->truncate();
        DB::table('dpb_wtf_activity_types')->truncate();
        DB::table('dpb_wtf_task_statuses')->truncate();
        DB::table('dpb_wtf_task_priorities')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // activity types
        $activityTypes = [
            ['code' => 'absence', 'title' => 'Neprítomnosť', 'description' => 'Neprítomnosť na pracovisku, pri ktorej zaniká nárok na prémiu'],
            ['code' => 'absence_2', 'title' => 'Neprítomnosť 2', 'description' => 'Neprítomnosť na pracovisku, pri ktorej nezaniká nárok na prémiu'],
            ['code' => 'custom_work', 'title' => 'Voliteľná pracovná činnosť', 'description' => 'Voliteľné pracovné činnosti podľa potrieb jednotlivých stredísk'],
            ['code' => 'catalogised_work', 'title' => 'Katalogizovaná pracovná činnosť', 'description' => 'Fixné pracovné činnosti dané predstavenstvom'],
        ];

        foreach ($activityTypes as $activityType) {
            ActivityType::create($activityType);
        }

        // activity statuses
        $activityStatuses = [
            ['code' => 'undone', 'title' => 'nesplnené'],
            ['code' => 'done', 'title' => 'splnené'],
        ];

        foreach ($activityStatuses as $status) {
            ActivityStatus::create($status);
        }

        // task statuses
        $taskStatuses = [
            ['code' => 'new', 'title' => 'otvorené'],
            ['code' => 'closed', 'title' => 'uzavreté'],
        ];

        foreach ($taskStatuses as $status) {
            TaskStatus::create($status);
        }

        // prorities
        $taskPriorities = [
            ['code' => 'low', 'title' => 'nízka'],
            ['code' => 'normal', 'title' => 'normálna'],
            ['code' => 'high', 'title' => 'vysoká'],
        ];

        foreach ($taskPriorities as $priority) {
            TaskPriority::create($priority);
        }
    }
}