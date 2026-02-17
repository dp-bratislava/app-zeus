<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CheckCorrectDepartments extends Command{

    private $tables = ['Temporary_Kontroly_autobusy', 'Temporary_Poruchy_autobusy','Temporary_poruchy_elektrika','Temporary_kontroly_elektrika'];

    protected $signature = 'app:check-correct-departments';

    protected $description = 'check whether ids point to real records in other tables';

    public function handle()
    {
        $allPairs = Collect();
        foreach ($this->tables as $table) {
            $this->info("Checking table: {$table}");
            $uniquePairs = DB::table($table)
                ->join('datahub_employee_contracts', "{$table}.employee_contract_id", '=', 'datahub_employee_contracts.id')
                ->select("{$table}.department_id", 'datahub_employee_contracts.PID')
                ->distinct() // This ensures the DB only returns unique combinations
                ->get()
                ->toArray();
            $allPairs = $allPairs->concat($uniquePairs);
        }
        $allPairs = $allPairs->unique(function ($item) {
        return $item->department_id . $item->PID;
        });

        
        foreach ($allPairs as $pair) {
            // find first worktime
            $worktimeDepartment = DB::table('dpb_worktimefund_model_worktime') 
            ->where('personal_id', $pair->PID) 
            
            ->first(); 

            if (!$worktimeDepartment) { $this->error("No worktime found for department_id: {$pair->department_id} and PID: {$pair->PID}"); continue; } 
            if ($worktimeDepartment->department != $pair->department_id) { $this->error("Mismatch for department_id: {$pair->department_id} and PID: {$pair->PID}. Worktime department: {$worktimeDepartment->department}"); }
            
        }

    }

}