<?php

namespace App\Models\Snapshots;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HRContract extends Model
{
    protected $table = 'mvw_hr_contract_snapshots';

    protected $fillable = []; 

    public function scopeByDepartmentCode(Builder $query, string $code): Builder
    {
        return $query->where('department_code', '=', $code);
    }    

    public function scopeByDepartmentCodes(Builder $query, array $codes): Builder
    {
        return $query->whereIn('department_codes', $codes);
    }    

    public function scopeByEmployeeCircuitCode(Builder $query, string $code): Builder
    {
        return $query->where('employee_circuit_code', '=', $code);
    }    

    public function scopeByEmployeeCircuitCodes(Builder $query, array $codes): Builder
    {
        return $query->whereIn('employee_circuit_code', $codes);
    }  
    
    public function scopeByPid(Builder $query, string $pid): Builder
    {
        return $query->where('pid', '=', $pid);
    }    

    public function scopeByPids(Builder $query, array $pids): Builder
    {
        return $query->whereIn('pid', $pids);
    }     

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('is_active', '=', 1);
    }       

    public function scopeIsPrimary(Builder $query): Builder
    {
        return $query->where('is_primary', '=', 1);
    }       

}
