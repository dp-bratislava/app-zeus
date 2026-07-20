<?php

namespace Database\Seeders\LocalDev;

use Dpb\Package\Tasks\Models\PlaceOfOrigin;
use Dpb\Package\Tasks\Models\TaskGroup;
use Illuminate\Database\Seeder;

class TaskReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $placesOfOrigin = [
            ['uri' => 'during-maintenance', 'title' => 'Počas údržby'],
            ['uri' => 'in-service', 'title' => 'V prevádzke'],
        ];

        foreach ($placesOfOrigin as $placeOfOrigin) {
            PlaceOfOrigin::firstOrCreate(
                ['uri' => $placeOfOrigin['uri']],
                ['title' => $placeOfOrigin['title']],
            );
        }

        $taskGroups = [
            ['code' => 'oprava', 'title' => 'Oprava'],
            ['code' => 'inspection', 'title' => 'Kontrola'],
            ['code' => 'daily-maintenance', 'title' => 'Denné ošetrenie'],
            ['code' => 'workshop-maintenance', 'title' => 'Dielenská údržba'],
        ];

        foreach ($taskGroups as $taskGroup) {
            TaskGroup::firstOrCreate(
                ['code' => $taskGroup['code']],
                ['title' => $taskGroup['title']],
            );
        }
    }
}
