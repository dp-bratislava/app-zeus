<?php

namespace Database\Seeders\DPB;

use App\Models\Datahub\Department;
use App\Models\DPB\DepartmentGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentGroupSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('dpb_department_groups')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // $departments = Department::all();
        // foreach ($departments as $department) {
        //     // create default group for department
        //     $group = DepartmentGroup::create([
        //         'code' => $department->code,
        //         'title' => $department->title,
        //     ]);

        //     // set default group for department
        //     $group->departments()->attach($department);
        // }
            
        $customGroups = [
            ['code' => 'bm', 'title' => 'Správa budov'],
            ['code' => 'fleet', 'title' => 'Správa vozidiel'],
        ];

        foreach ($customGroups as $customGroup) {
            // create default group for department
            $group = DepartmentGroup::create([
                'code' => $customGroup['code'],
                'title' => $customGroup['title'],
            ]);

            // set default group for department
            // $group->departments()->attach($department);
        }
    }
}
