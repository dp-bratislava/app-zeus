<?php

namespace Database\Seeders\TS;

use App\Models\TS\ActivityStatus;
use App\Models\TS\TicketGroup;
use App\Models\TS\TicketPriority;
use App\Models\TS\TicketStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnumSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('dpb_ts_activity_statuses')->truncate();
        DB::table('dpb_ts_ticket_statuses')->truncate();
        DB::table('dpb_ts_ticket_priorities')->truncate();
        DB::table('dpb_ts_ticket_groups')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // activity statuses
        $activityStatuses = [
            ['code' => 'undone', 'title' => 'nesplnené'],
            ['code' => 'done', 'title' => 'splnené'],
        ];

        foreach ($activityStatuses as $status) {
            ActivityStatus::create($status);
        }

        // task statuses
        $ticketStatuses = [
            ['code' => 'new', 'title' => 'otvorené'],
            ['code' => 'closed', 'title' => 'uzavreté'],
        ];

        foreach ($ticketStatuses as $status) {
            TicketStatus::create($status);
        }

        // priorities
        $ticketPriorities = [
            ['code' => 'low', 'title' => 'nízka'],
            ['code' => 'normal', 'title' => 'normálna'],
            ['code' => 'high', 'title' => 'vysoká'],
        ];

        foreach ($ticketPriorities as $priority) {
            TicketPriority::create($priority);
        }

        // groups
        $ticketGroups = [
            ['code' => 'it', 'title' => 'IT'],
            ['code' => 'fleet', 'title' => 'Vozidlá'],
            ['code' => 'building_management', 'title' => 'Správa budov'],
        ];

        foreach ($ticketGroups as $group) {
            TicketGroup::create($group);
        }        
    }
}