<?php

namespace App\Services\Inspection;

use App\Services\Fleet\VehicleService;
use App\UseCases\InspectionAssignment\CreateInspectionAssignmentUseCase;
use Dpb\Package\Inspections\Models\Inspection;
use Dpb\Package\Fleet\Models\Vehicle;
use Dpb\Package\Fleet\Models\VehicleModel;
use Dpb\Package\Inspections\Models\InspectionTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class UpcommingInspectionService
{
    public function __construct(
        protected Vehicle $vehicleRepo,
        protected VehicleModel $vehicleModelRepo,
        protected VehicleService $vehicleService,
        protected CreateInspectionAssignmentUseCase $createInspectionAssignmentUseCase,
        protected InspectionTemplate $templateRepo,
        protected TemplateAssignmentService $templateAssignmentSvc
    ) {}

    public function generate()
    {
        // check all vehicles
        $vehicleModels = $this->vehicleModelRepo->all();
        foreach ($vehicleModels as $vehicleModel) {

            // based on condition met, get upcoming inspection template
            // TO DO
            // get all inspection templates associated 
            // with this vehicle via vehicle model
            $templates = $this->templateAssignmentSvc->getTemplatesBySubject($vehicleModel);
            if ($templates->isEmpty()) {
                continue;
            }

            // for all vehicles of this model 
            foreach ($vehicleModel->vehicles as $vehicle) {
                foreach ($templates as $template) {
                    // if inspection template is eligible for creation
                    if ($this->inspectionTresholdReached($vehicle, $template)) {
                        $data = [
                            'date' => Carbon::now()->format('Y-m-d'),
                            'template_id' => $template->id,
                            'subject_id' => $vehicle->id
                        ];

                        $this->createInspectionAssignmentUseCase->execute($data);
                    }
                }
            }

            // $inspectionTreshold = 200;
            // $totalDistanceTraveled = $this->vehicleService->getTotalDistanceTraveled($vehicle);
            // if ($totalDistanceTraveled >= $inspectionTreshold) {
            //     $this->inspectionSvc->create($vehicle, $template);
            // }
        }
    }

    // public function generate()
    // {
    //     // check all vehicles
    //     $vehicles = $this->vehicleRepo->all();
    //     foreach ($vehicles as $vehicle) {

    //         // based on condition met, get upcoming inspection template
    //         // TO DO
    //         // get all inspection templates associated 
    //         // with this vehicle via vehicle model
    //         $templates = $this->templateRepo->all();
    //         foreach ($templates as $template) {
    //             if ($this->inspectionTresholdReached($vehicle, $template)) {
    //                 $this->inspectionSvc->create($vehicle, $template);                    
    //             }
    //         }

    //         // $inspectionTreshold = 200;
    //         // $totalDistanceTraveled = $this->vehicleService->getTotalDistanceTraveled($vehicle);
    //         // if ($totalDistanceTraveled >= $inspectionTreshold) {
    //         //     $this->inspectionSvc->create($vehicle, $template);
    //         // }
    //     }
    // }

    protected function inspectionTresholdReached($vehicle, $template)
    {
        $inspectionTreshold = $template->treshold()?->value;
        if ($inspectionTreshold == null) {
            return false;
        }
        return $inspectionTreshold < $this->vehicleService->getTotalDistanceTraveled($vehicle);
    }
}
