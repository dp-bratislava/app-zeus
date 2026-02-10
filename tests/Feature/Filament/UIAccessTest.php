<?php

namespace Tests\Feature\Filament;

use Tests\TestCase;
use App\Models\User;
use Dpb\DpbEmployeeManager\Filament\Pages\EmployeeManagerPage\EmployeeManagerPage;
use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionAssignmentResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Inspection\InspectionTemplateResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\PlaceOfOriginResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskItemGroupResource;
use Dpb\Package\TaskMS\UI\Filament\Resources\Task\TaskItemResource;
use Dpb\WorkTimeFund\Filament\Pages\WorktimeSchedulePlannerPage;
use Dpb\WorkTimeFundFilament\Filament\Pages\WorktimeManagementPage;
use Dpb\WtfTmsBridge\Filament\Resources\Task\DailyMaintenanceResource;
use Dpb\WtfTmsBridge\Filament\Resources\Task\TaskAssignmentResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\FilamentPermissionAssertions;

class UIAccessTest extends TestCase
{
    // use RefreshDatabase; // <-- handles migrations automatically
    use FilamentPermissionAssertions;

    public function test_predavacky_veduci_can_access()
    {
        $user = User::where('login', '=', 'predavacky')->first();

        $pages = [
            WorktimeSchedulePlannerPage::class,
            WorktimeManagementPage::class,
            EmployeeManagerPage::class,
        ];

        foreach ($pages as $page) {
            $this->assertTrue(
                $this->assertUserCanAccessPage($user, $page),
                "User '{$user->login}' should have access to {$page}"
            );
        }
    }

    public function test_predavacky_veduci_can_not_access()
    {
        $user = User::where('login', '=', 'predavacky')->first();
        $resources = [
            // task
            PlaceOfOriginResource::class,
            TaskItemGroupResource::class,
            TaskItemResource::class,
            // DailyMaintenanceResource::class,
            // TaskAssignmentResource::class,
            // inspection
            InspectionAssignmentResource::class,
            InspectionTemplateResource::class
        ];

        foreach ($resources as $resource) {
            $this->assertUserCannotAccessResource($user, $resource);
        }        
        // $this->assertUserCannotAccessResource($user, PlaceOfOriginResource::class);
        // $this->assertUserCannotAccessResource($user, TaskItemGroupResource::class);
        // $this->assertUserCannotAccessResource($user, TaskItemResource::class);
        // $this->assertUserCannotAccessResource($user, DailyMaintenanceResource::class);
        // $this->assertUserCannotAccessResource($user, TaskAssignmentResource::class);
    }

    public function test_veduci_pracovnik_can_access()
    {
        $user = User::where('login', '=', 'T0001')->first();

        $pages = [
            // WorktimeSchedulePlannerPage::class,
            WorktimeManagementPage::class,
            EmployeeManagerPage::class,
        ];

        foreach ($pages as $page) {
            $this->assertTrue(
                $this->assertUserCanAccessPage($user, $page),
                "User '{$user->login}' should have access to {$page}"
            );
        }

        $resources = [
            // task
            PlaceOfOriginResource::class,
            TaskItemGroupResource::class,
            TaskItemResource::class,
            // DailyMaintenanceResource::class,
            // TaskAssignmentResource::class,
            // inspection
            InspectionAssignmentResource::class,
            InspectionTemplateResource::class
        ];       
        
        foreach ($resources as $resource) {
            $this->assertUserCanAccessResource($user, $resource);
        }             
    }

    public function test_superadmin_permissions()
    {
        // $super = User::factory()->create();
        $super = User::where('login', '=', 'admin')->first();
        $super->assignRole('super-admin');

        // superadmin can do everything
        $permissions = ['read', 'create', 'update', 'delete'];

        $this->assertResourcePermissions($super, PlaceOfOriginResource::class, $permissions);
    }
}
