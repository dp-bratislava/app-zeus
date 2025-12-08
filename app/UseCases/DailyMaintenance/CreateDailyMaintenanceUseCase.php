<?php

namespace App\UseCases\DailyMaintenance;

use App\Data\Inspection\InspectionAssignmentData;
use App\Data\Inspection\InspectionData;
use App\Models\InspectionAssignment;
use App\Services\InspectionAssignmentService;
use Illuminate\Contracts\Auth\Guard;
use App\States;
use App\UseCases\TicketAssignment\CreateFromDailyMaintenanceUseCase;
use Illuminate\Support\Carbon;

class CreateDailyMaintenanceUseCase
{
    public function __construct(
        private InspectionAssignmentService $inspectionAssignmentSvc,
        private CreateFromDailyMaintenanceUseCase $createTicketAssignmentUseCase,
        private Guard $guard,
    ) {}

    public function execute(array $data): ?InspectionAssignment
    {

        foreach ($data['vehicles'] as $vehicleId) {
            // inspection item
            $inspectionData = new InspectionData(
                null,
                Carbon::parse($data['date']),
                $data['inspection-template'] ?? null,
                States\Inspection\Upcoming::$name,
            );

            // create inspection assignment
            $iaData = new InspectionAssignmentData(
                null,
                $inspectionData,
                $vehicleId,
                'vehicle',
                // $this->guard->id(),
            );

            $dailyMaintenance = $this->inspectionAssignmentSvc->create($iaData);

            // create ticket assignment
            $this->createTicketAssignmentUseCase->execute($dailyMaintenance);
        }


        return null; //$dailyMaintenance;
    }
}
