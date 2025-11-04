<?php

namespace App\Services;

use App\Models\ActivityAssignment;
use Dpb\Package\Activities\Models\Activity;
use Illuminate\Database\Eloquent\Model;

class ActivityAssignmentRepository
{


    public function syncActivities(Model $subject, array $activitiesData) {
        foreach ($activitiesData as $key => $activityData) {
            // create 
            if (!isset($activityData['id']) || ($activityData['id'] == null)) {
                $activity = Activity::create($activityData);
                $activityAssignment = new ActivityAssignment();
                $activityAssignment->activity()->associate($activity);
                $activityAssignment->subject()->associate($subject);
                $activityAssignment->save();
            }
            // update
            else {
                $activityAssignment = ActivityAssignment::whereMorphedTo('subject', $subject)->get();
                $activityAssignment->activity->update(['id' => $activityData['id']], $activityData);
            }
            
            // delete
            // foreach ($activityAssignments as $key => $activityAssignment) {
            //     $activityAssignment->updateOrCreate(['id' => $activityData['id'] ?? null], $activityData);
            // }
        }
    } 
}
