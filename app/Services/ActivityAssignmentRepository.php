<?php

namespace App\Services;

use App\Models\ActivityAssignment;
use Dpb\Package\Activities\Models\Activity;
use Illuminate\Database\Eloquent\Model;

class ActivityAssignmentRepository
{
    public function __construct(
        protected Activity $activityModel,
        protected ActivityAssignment $activityAssignmentModel,
        protected WorkAssignmentRepository $workAssignmentRepo
    ) {}

    public function syncActivities(Model $subject, array $activitiesData)
    {
        foreach ($activitiesData as $key => $activityData) {
            // dd($activityData);
            // create 
            if (!isset($activityData['id']) || ($activityData['id'] == null)) {
                // activity
                $activity = $this->activityModel->create($activityData);
                // activity assignment
                $activityAssignment = $this->activityAssignmentModel->newInstance();
                $activityAssignment->activity()->associate($activity);
                $activityAssignment->subject()->associate($subject);
                $activityAssignment->save();
                // work
                $this->workAssignmentRepo->syncWork($activity, $activityData['workAssignments']);
            }
            // update
            else {
                // dd($activityData);
                $activity = $this->activityModel->findSole($activityData['id']);
                $activity->update($activityData);
                // work
                $this->workAssignmentRepo->syncWork($activity, $activityData['workAssignments']);
            }

        }

        // delete
        $activityIds = collect($activitiesData)->pluck('id');
        $this->activityAssignmentModel->whereMorphedTo('subject', $subject)
            ->whereNotIn('activity_id', $activityIds)
            ->delete();
        // foreach ($activityAssignments as $key => $activityAssignment) {
        //     $activityAssignment->updateOrCreate(['id' => $activityData['id'] ?? null], $activityData);
        // }
    }
}
