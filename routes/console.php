<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('datahub-sync:update --models=Hierarchy,ContractType,Department,EmployeeCircuit,Employee,Profession,EmployeeContract')->hourly();