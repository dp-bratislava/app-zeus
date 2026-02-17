<?php

namespace App\Console\Commands;



use App\Console\Services\CustomizedAttendanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SyncWorktimes extends Command{

    // private $tables = ['Temporary_Kontroly_autobusy', 'Temporary_Poruchy_autobusy','Temporary_poruchy_elektrika','Temporary_kontroly_elektrika'];   
    private $tables = ['combined_table_kontroly', 'combined_table_poruchy'];

    protected $signature = 'app:sync-worktimes-using-date';

    public function handle()
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
            app(CustomizedAttendanceService::class)->prefillEmployeeAttendance(departmentId: $departmentId, customDate: '2026-01-01');
        }
    }

}