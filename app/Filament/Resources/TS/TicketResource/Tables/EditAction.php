<?php

namespace App\Filament\Resources\TS\TicketResource\Tables;

use App\Services\TS\CreateTicketService;
use App\Services\TS\ActivityService;
use App\Services\TS\HeaderService;
use App\Services\TS\SubjectService;
use Filament\Tables\Actions\EditAction as BaseEditAction;
use Illuminate\Database\Eloquent\Model;

class EditAction
{
    public static function make(): BaseEditAction
    {
        return BaseEditAction::make()
            ->mutateRecordDataUsing(function (
                $record,
                array $data,
                SubjectService $subjectSvc,
                HeaderService $headerSvc,
                ActivityService $activitySvc
            ): array {
                $data['subject_id'] = $subjectSvc->getSubject($record)?->id;
                $data['department_id'] = $headerSvc->getHeader($record)?->department?->id;
                $data['source_id'] = $record?->source?->id;
                // $activities = $activitySvc->getActivities($record);
                // foreach ($activities as $activity) {
                //     $data['activities'][] = [
                //             'id' => $activity->id,
                //             'date' => $activity->date,
                //             // 'activity_template_id' => $activity->template_id
                //     ];
                // }
                // $data['activities'] = $activitySvc->getActivities($record)
                //     ->map(function($activity) {
                //         return [
                //             'date' => $activity->date,
                //             'activity_template_id' => $activity->template_id
                //         ];
                //     });
                $data['activities'] = $activitySvc->getActivities($record);
                return $data;
            })
            ->using(function (Model $record, array $data, CreateTicketService $ticketSvc): ?Model {
                return $ticketSvc->update($record, $data);
            });
    }
}
