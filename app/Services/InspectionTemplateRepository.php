<?php

namespace App\Services;

use App\Models\IncidentAssignment;
use App\States;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Incidents\Models\Incident;
use Dpb\Package\Inspections\Models\InspectionTemplate;
use Dpb\Package\Inspections\Models\TemplateCondition;
use Dpb\Package\Inspections\Models\TemplateConditionType;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Relations\Relation;

class InspectionTemplateRepository
{
    protected const TRESHOLD_CODE = 'treshold';
    protected const TRESHOLD_TITLE = 'Interval';
    protected const FIRST_ADVANCE_CODE = '1-advance';
    protected const FIRST_ADVANCE_TITLE = 'Prvý predstih';
    protected const SECOND_ADVANCE_CODE = '2-advance';
    protected const SECOND_ADVANCE_TITLE = 'Druhý predstih';


    public function __construct(
        protected InspectionTemplate $inspectionTemplateModel,
        protected TemplateCondition $templateConditionModel,
        // protected Relation $relation,
        protected Guard $guard
    ) {}

    public function create(array $data): InspectionTemplate
    {
        // dd($data);
        // create inspection template
        $inspectionTemplate = $this->inspectionTemplateModel->create([

        ]);

        // conditions
        $distanceConditionTypeId = TemplateConditionType::byCode('distance_traveled')->first()->id;
        if (isset($data['cnd_distance_treshold'])) {
            $this->createCondition([
                'code' => self::TRESHOLD_CODE,
                'title' => self::TRESHOLD_TITLE,
                'template_id' => $inspectionTemplate->id,
                'condition_type_id' => $distanceConditionTypeId,
                'value' => $data['cnd_distance_treshold']
            ]);
        }

        return $inspectionTemplate;
    }

    // public function update(Incident $incident, array $data)
    // {
    //     // set default state
    //     $data['state'] = $data['state'] ?? States\Incident\Created::$name;

    //     // create incident
    //     $incident->update($data);

    //     // subject
    //     // $subject = $this->relation->getMorphedModel('vehicle')::find($data['subject_id']);

    //     // create incident assigment
    //     $incidentAssignemnt = $this->incidentAssignment->whereBelongsTo($incident)->first();
    //     $incidentAssignemnt->incident()->associate($incident);
    //     $incidentAssignemnt->subject_id = $data['subject_id'];
    //     $incidentAssignemnt->subject_type = self::SUBJECT_TYPE;

    //     // $incidentAssignemnt->subject()->associate(Vehicle::find($data['subject_id']));
    //     // $incidentAssignemnt->subject()->associate($subject);
    //     // $incidentAssignemnt->author()->associate($this->guard->user());
    //     $incidentAssignemnt->save();

    //     return $incident;
    // }

    // protected function getSubject(array $data) {
    //     return Vehicle::find($data)
    // }

    protected function createCondition(array $data) : TemplateCondition 
    {
        return $this->templateConditionModel->create($data);
    }
}
