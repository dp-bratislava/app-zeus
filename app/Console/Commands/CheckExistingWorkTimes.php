<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CheckExistingWorkTimes extends Command{

    private $departmentsAutobusy = ['7213','7223','7233'];
    private $departmentsElektrika = ['5400', '5521','5621','5522','5622'];
    private $departmentsToCheck = [''];
    // private $tables = ['Temporary_Kontroly_autobusy', 'Temporary_Poruchy_autobusy','Temporary_poruchy_elektrika','Temporary_kontroly_elektrika'];
    private $tables = ['combined_table_kontroly', 'combined_table_poruchy'];

    protected $signature = 'app:check-existing-work-times';


    public function handle()
    {
        $this->departmentsToCheck = [...$this->departmentsAutobusy, ...$this->departmentsElektrika];

        $allPairs = Collect();
        $departmentIds = DB::table('datahub_departments')
            ->whereIn('code', $this->departmentsToCheck)
            ->pluck('id');
            
        foreach ($this->tables as $table) {
            $this->info("Checking table: {$table}");
            $uniquePairs = DB::table($table)
                ->join('datahub_employee_contracts', "{$table}.employee_contract_id", '=', 'datahub_employee_contracts.id')
                ->selectRaw("{$table}.department_id, datahub_employee_contracts.PID, DATE_FORMAT(STR_TO_DATE({$table}.`Dátum výkonu pracovníka`, '%d.%m.%Y'), '%Y-%m-%d') as work_date")
                ->whereIn("{$table}.department_id", $departmentIds)
                ->distinct() // This ensures the DB only returns unique combinations
                ->get()
                ->toArray();
            $allPairs = $allPairs->concat($uniquePairs);
        }
        $allPairs = $allPairs->unique(function ($item) {
        return $item->department_id . $item->PID . $item->work_date;
        });

        $employeeNames = DB::table('datahub_employee_contracts')
            ->join('datahub_employees', 'datahub_employee_contracts.datahub_employee_id', '=', 'datahub_employees.id')
            ->select('PID', DB::raw('CONCAT(datahub_employees.last_name, " ", datahub_employees.first_name) as name'))
            ->pluck('name', 'PID');

        $departmentCodes = DB::table('datahub_departments')
            ->whereIn('code', $this->departmentsToCheck)
            ->pluck('code', 'id');

        foreach ($allPairs as $pair) {
            // find first worktime
            $worktimeDepartment = DB::table('dpb_worktimefund_model_worktime') 
            ->where('personal_id', $pair->PID) 
            ->where('department', $pair->department_id)
            ->where('date', $pair->work_date)
            ->first(); 

            if (!$worktimeDepartment && $pair->work_date > '2026-01-01') { 
                $employeeName = $employeeNames->get($pair->PID, 'Unknown Employee');
                $departmentCode = $departmentCodes->get($pair->department_id, 'Unknown Department');
                $formatedDate = \Carbon\Carbon::parse($pair->work_date)->format('d.m.Y');
                $this->error("{$departmentCode};{$pair->PID};{$employeeName};{$formatedDate}"); 
                continue; 
            } 
            
        }

    }

}