<?php

namespace App\Console\Commands;

use Dpb\WorkTimeFund\Services\AttendanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SyncWorktimesForAsphereImport extends Command{

    // private $tables = ['Temporary_Kontroly_autobusy', 'Temporary_Poruchy_autobusy','Temporary_poruchy_elektrika','Temporary_kontroly_elektrika'];   
    private $tables = ['combined_table_kontroly', 'combined_table_poruchy'];

    protected $signature = 'app:sync-worktimes-asphere-import';

    public function handle(AttendanceService $attendanceService)
    {
        $allEmployeeContractIds = collect();

        foreach ($this->tables as $table) {
            $userContractIds = DB::table($table)
                ->select('employee_contract_id')
                ->distinct()
                ->pluck('employee_contract_id')
                ->filter(); 
            $allEmployeeContractIds = $allEmployeeContractIds->merge($userContractIds);
        }
        
        $uniqueDepartmentIds = DB::table('datahub_employee_contracts')
            ->select('datahub_department_id')
            ->whereIn('id', $allEmployeeContractIds->unique())
            ->distinct()
            ->pluck('datahub_department_id')
            ->filter();

        $this->info("Found " . $uniqueDepartmentIds->count() . " unique departments to sync.");
        
        foreach ($uniqueDepartmentIds as $departmentId) {
            $this->info("Syncing worktimes for department ID: {$departmentId}...");
            $attendanceService->prefillEmployeeAttendance(departmentId: $departmentId, customDate: '2026-01-01');
        }
    }

}