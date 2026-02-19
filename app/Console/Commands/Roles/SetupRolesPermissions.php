<?php

namespace App\Console\Commands\Roles;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SetupRolesPermissions extends Command
{
    protected $signature = 'app:roles-setup';
    protected $description = 'Set up default roles with permissions seeded from packages';

    public function handle()
    {
        $map = [
            'super-admin' => [
                '%'
            ],
            'veduci-predavacky' => [
                // enable department picker                
                'dpb-departments.component-access.department-switcher-component',
                'dpb-departments.department.read_assigned',
                'dpb-departments.department_switcher',
                // enable role specific options
                'dpb-wtff.access.worktime-management-page',
                'dpb-wtf.access.worktime-schedule-planner-page',
                'dpb-work-time-fund-filament.page-access.worktime-management-page',
                'dpb-work-time-fund.page-access.worktime-schedule-planner-page',
                // employee groups 
                'dpb-employee-manager.page-access.employee-manager-page',
                'dpb-mpg.dpb_employeemanager_model_group.%',
                // reports / insights
                'dpb-insights.page-access.insights-page',
            ],
            'veduci-revizori' => [
                // enable department picker                
                'dpb-departments.component-access.department-switcher-component',
                'dpb-departments.department.read_assigned',
                'dpb-departments.department_switcher',
                // enable role specific options
                'dpb-wtff.access.worktime-management-page',
                'dpb-wtf.access.worktime-schedule-planner-page',
                'dpb-work-time-fund-filament.page-access.worktime-management-page',
                'dpb-work-time-fund.page-access.worktime-schedule-planner-page',
                // employee groups 
                'dpb-employee-manager.page-access.employee-manager-page',
                'dpb-mpg.dpb_employeemanager_model_group.%',
                // reports / insights
                'dpb-insights.page-access.insights-page',
            ],
            'dopravny-dispecing' => [
                // enable department picker                
                'dpb-departments.component-access.department-switcher-component',
                'dpb-departments.department.read_assigned',
                'dpb-departments.department_switcher',
                // enable role specific options
                'pkg-tickets.ticket.%',
                // reports / insights
                'dpb-insights.page-access.insights-page',
            ],
            'veduci-pracovnik' => [
                'pkg-eav%',
                'pkg-fleet%',
                'pkg-inspections%',
                'pkg-tasks%',
                'pkg-task-ms%',
                'pkg-tickets%',
                // employee groups 
                'dpb-employee-manager.page-access.employee-manager-page',
                'dpb-mpg.dpb_employeemanager_model_group.%',
                // reports / insights
                'dpb-insights.page-access.insights-page',
            ],
            // add new roles here

            // 'pkg-eav%' 
            // 'pkg-fleet%' 
            // 'pkg-inspections%' 
            // 'pkg-tasks%' 
            // 'pkg-task-ms%' 
            // 'pkg-tickets%'             
        ];

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function () use ($map) {
            $guardName = 'web';
            $now = now();
            foreach ($map as $roleName => $permissionNames) {
                // create role
                $role = Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => $guardName,
                    // 'created_at' => $now,
                    // 'updated_at' => $now,
                ]);

                // add permissions to role
                foreach ($permissionNames as $permissionName) {
                    $permissions = Permission::whereLike('name', $permissionName)
                        ->where('guard_name', '=', $guardName)
                        ->get();

                    if ($permissions) {
                        $role->givePermissionTo($permissions);
                    }
                }
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('Roles synced successfully.');
    }
}
