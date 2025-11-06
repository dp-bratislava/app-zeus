<?php

namespace App\Services;

use App\Models\ActivityAssignment;
use App\Models\Datahub\EmployeeContract;
use App\Models\WorkAssignment;
use Dpb\Package\Activities\Models\Activity;
use Dpb\Packages\WorkLog\Models\WorkInterval;
use Illuminate\Database\Eloquent\Model;

class WorkAssignmentRepository
{
    public function __construct(
        // protected Activity $workIntervalModel,
        protected EmployeeContract $employeeContractModel,
        protected WorkInterval $workIntervalModel,
        protected WorkAssignment $workAssignmentModel,
        // protected ActivityAssignment $activityAssignmentModel,
    ) {}

    public function syncWork(Model $subject, array $workAssignmentsData)
    {
        foreach ($workAssignmentsData as $key => $workAssignmentData) {
            $contract = $this->employeeContractModel->findSole($workAssignmentData['employee_contract_id']);

            // create 
            if (!isset($workAssignmentData['id']) || ($workAssignmentData['id'] == null)) {
                // work interval
                $workInterval = $this->workIntervalModel->create($workAssignmentData['work_interval']);
                // work assignment
                $workAssignment = $this->workAssignmentModel->newInstance();
                $workAssignment->workInterval()->associate($workInterval);
                $workAssignment->employeeContract()->associate($contract);
                $workAssignment->subject()->associate($subject);
                $workAssignment->save();
            }
            // update
            else {
                // work assignment
                $workAssignment = $this->workAssignmentModel
                    ->with('workInterval')
                    ->findSole($workAssignmentData['id']);
                $workAssignment->employeeContract()->associate($contract);
                $workAssignment->workInterval->update($workAssignmentData['work_interval']);
                $workAssignment->save();
            }
        }

        // delete
        $workIntervalsIds = collect($workAssignmentsData)->pluck('id');
        $this->workAssignmentModel->whereMorphedTo('subject', $subject)
            ->whereNotIn('work_interval_id', $workIntervalsIds)
            ->delete();
        // foreach ($activityAssignments as $key => $activityAssignment) {
        //     $activityAssignment->updateOrCreate(['id' => $activityData['id'] ?? null], $activityData);
        // }
    }
}
