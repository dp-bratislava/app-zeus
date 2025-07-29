<?php

namespace Database\Seeders\Fleet;

use App\Models\Fleet\Inspection\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InspectionEnumSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('dpb_fleet_inspection_statuses')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // inspeciton statuses
        $inspecitonStatuses = [
            ['code' => 'planned', 'title' => 'Evidovaná'],
            ['code' => 'overdue', 'title' => 'Po termíne'],
            ['code' => 'ongoing', 'title' => 'Prebieha'],
            ['code' => 'ahead', 'title' => 'Pred termínom'],
            ['code' => 'finished.no-distance-known', 'title' => 'Vykonaná - nezistené km'],
            ['code' => 'finished.fail', 'title' => 'Vykonaná neúspešne'],
            ['code' => 'finished.with-failures', 'title' => 'Vykonaná s výhradami'],
            ['code' => 'finished.success', 'title' => 'Vykonaná úspešne'],
        ];

        foreach ($inspecitonStatuses as $inspecitonStatus) {
            Status::create($inspecitonStatus);
        }
    }
}
