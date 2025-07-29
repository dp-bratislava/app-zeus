<?php

namespace Database\Seeders\TS;

use App\Models\TS\Issue\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IssueEnumSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('dpb_ts_issue_statuses')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // task statuses
        $issueStatuses = [
            ['code' => 'new', 'title' => 'Nová porucha', 'is_default' => true],
            ['code' => 'open.in-progress', 'title' => 'Neukončené - na vozidle sa pracuje'],
            ['code' => 'open.missing-spare-part', 'title' => 'Neukončené - chýba náhradný diel'],
            ['code' => 'open.ready-to-close', 'title' => 'Neukončené - pripravené na ukončenie'],
            ['code' => 'open.missing-data', 'title' => 'Neukončené - nevyplnené údaje'],
            ['code' => 'closed.success', 'title' => 'Ukončené - porucha odstránená'],
            ['code' => 'closed.operable-with-issues', 'title' => 'Ukončené - prevádzkyschopné s poruchou'],
            ['code' => 'assigned', 'title' => 'Prevzatá'],
        ];
		
        foreach ($issueStatuses as $issueStatus) {
            Status::create($issueStatus);
        }
    }
}
