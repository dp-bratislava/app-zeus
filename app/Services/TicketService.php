<?php

namespace App\Services;

use App\Data\ActivityData;
use App\Data\ActivityTemplateData;
use App\Data\MaterialData;
use App\Data\TicketData;
use App\Data\WorkIntervalData;
use App\Models\ActivityAssignment;
use App\Models\Expense\Material;
use Dpb\Package\Activities\Models\Activity;
use Dpb\Package\Activities\Models\ActivityTemplate;
use Dpb\Package\Tickets\Models\Ticket;
use Spatie\LaravelData\DataCollection;

// use Illuminate\Database\Eloquent\Collection;

class TicketService
{
    public function getTickets()
    {
        // 1. Load tickets
        $tickets = Ticket::all();
            
        // 2. Load activity assignments for tickets
        $activityAssignments = ActivityAssignment::where('subject_type', 'ticket')
            ->whereIn('subject_id', $tickets->pluck('id'))
            ->get();

        $materials = Material::whereIn('ticket_id', $tickets->pluck('id'))
            ->get();

        // 3. Load all activities in bulk
        $activityIds = $activityAssignments->pluck('activity_id');
        $activities = Activity::whereIn('id', $activityIds)->get()->keyBy('id');

        // 3. Load all activities in bulk
        $activityTempalteIds = $activities->pluck('activity_template_id');
        $activityTemplates = ActivityTemplate::whereIn('id', $activityTempalteIds)->get()->keyBy('id');

        // 4. Load all expenses for activities
        // $expenses = Expense::where('rateable_type', Activity::class)
        //     ->whereIn('rateable_id', $activityIds)
        //     ->get()
        //     ->groupBy('rateable_id');

        // 5. Load all unit rates for activities (polymorphic)
        // $unitRates = UnitRate::with(['unit', 'currency'])
        //     ->where('rateable_type', Activity::class)
        //     ->whereIn('rateable_id', $activityIds)
        //     ->get()
        //     ->groupBy('rateable_id');

        // 6. Group assignments by ticket
        $assignmentsByTicket = $activityAssignments->groupBy('subject_id');
        $materialsByTicket = $materials->groupBy('ticket_id');

        // 7. Map into DTOs
        return $tickets->map(fn($ticket) => new TicketData(
            $ticket->id,
            $ticket->title,
            $ticket->description,
            new DataCollection(
                ActivityData::class,
                ($assignmentsByTicket[$ticket->id] ?? collect())->map(function ($assignment) use ($activities, $activityTemplates) {
                    $activity = $activities[$assignment->activity_id];
                    $template = $activityTemplates[$activity->activity_template_id];

                    return new ActivityData(
                        $activity->id,
                        new ActivityTemplateData(
                            $template->id,
                            $template->title,
                            $template->duration,
                            $template->is_catalogised,
                            $template->people,
                        ),
                        new DataCollection(WorkIntervalData::class, [])
                    );
                })->toArray()
                // activities: new DataCollection(ActivityData::class, [])
                // activities: ($assignmentsByTicket[$ticket->id] ?? collect())->map(fn($assignment) => {
                //     $activity = $activities[$assignment->activity_id];

                //     return new ActivityData(
                //         id: $activity->id,
                //         template: 
                //         // name: $activity->name,
                //         // expenses: ($expenses[$activity->id] ?? collect())->map(fn($expense) => new ExpenseData(
                //         //     id: $expense->id,
                //         //     unit_price: $expense->unit_price,
                //         //     description: $expense->description
                //         // ))->toArray(),
                //         // unitRates: ($unitRates[$activity->id] ?? collect())->map(fn($rate) => new UnitRateData(
                //         //     id: $rate->id,
                //         //     unit_price: $rate->unit_price,
                //         //     unit_code: $rate->unit->code,
                //         //     currency_code: $rate->currency->code
                //         // ))->toArray()
                //     );
                // })->toArray()
            ),
            30,
            new DataCollection(
                MaterialData::class,
                ($materialsByTicket[$ticket->id] ?? collect())->map(function ($material) {
                    return new MaterialData(
                        $material->id,
                        $material->date,
                        $material->code,
                        $material->title,
                        $material->description,
                        $material->price,
                        $material->vat,
                    );
                })->toArray()
                // activities: new DataCollection(ActivityData::class, [])
                // activities: ($assignmentsByTicket[$ticket->id] ?? collect())->map(fn($assignment) => {
                //     $activity = $activities[$assignment->activity_id];

                //     return new ActivityData(
                //         id: $activity->id,
                //         template: 
                //         // name: $activity->name,
                //         // expenses: ($expenses[$activity->id] ?? collect())->map(fn($expense) => new ExpenseData(
                //         //     id: $expense->id,
                //         //     unit_price: $expense->unit_price,
                //         //     description: $expense->description
                //         // ))->toArray(),
                //         // unitRates: ($unitRates[$activity->id] ?? collect())->map(fn($rate) => new UnitRateData(
                //         //     id: $rate->id,
                //         //     unit_price: $rate->unit_price,
                //         //     unit_code: $rate->unit->code,
                //         //     currency_code: $rate->currency->code
                //         // ))->toArray()
                //     );
                // })->toArray()
            ),                        
        ))->toArray();
    }
    // public function __construct(protected EntityRelationService $erService) {}

    // public function assignVehicle(Ticket $ticket, Vehicle $vehicle)
    // {
    //     $this->erService->createRelation($ticket, $vehicle, 'assigned');
    // }

    // // public function getVehicles(Ticket $ticket)
    // // {
    // //     return $this->erService->getTargetsOfType($ticket, Vehicle::class);
    // // }

    // public function getVehicle(Ticket $ticket)
    // {
    //     return $this->erService
    //         ->getTargetsOfType($ticket, Vehicle::class)
    //         // ->firstOrFail()
    //         ->first()
    //         ?->target;
    // }

    // public function assignDepartment(Ticket $ticket, Department $department)
    // {
    //     $this->erService->createRelation($ticket, $department, 'assigned');
    // }    

    // public function getDepartment(Ticket $ticket): Department
    // {
    //     return $this->erService
    //         ->getTargetsOfType($ticket, Department::class)
    //         ->firstOrFail()
    //         ?->target;
    // }    
}
