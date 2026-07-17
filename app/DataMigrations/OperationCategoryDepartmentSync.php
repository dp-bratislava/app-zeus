<?php

namespace App\DataMigrations;

use App\DataMigrations\Contracts\DataMigration;
use Dpb\WorkTimeFund\Models\Category;
use Dpb\WorkTimeFund\Services\OperationCategorySyncService;

/**
 * Set all operations as scalable for department 9486
 */
class OperationCategoryDepartmentSync implements DataMigration
{
    public function __construct(
        private OperationCategorySyncService $ocsService
    ) {}

    public function run(): void
    {
        // get main operation categories for maintenacne group deaprtments
        $categoryIds = [
            // DA 
            '181', // autobusy            
            '5575', // zazemne prace
            // DE 
            '693', // elektricky 
            '5533', // e-5400
            // '', ''
            // DT 
            '696', // trolejbusy  
            '5573', // ine
            '5574', // zazemne prace

            // common 
            '5572', // daily maintnence
            '3', // doprava            
        ];

        // get main categories
        $mainCategories = Category::whereIn('id', $categoryIds)->get();
                
        // sync departments for this main categories descendants 
        foreach ($mainCategories as $mainCategory) {
            $this->ocsService->syncDepartments($mainCategory->descendants, $mainCategory->departments);
        }

        // @TODO
        // needs to be more complex
        // sync vehicle models for this main categories descendants 
        // foreach ($mainCategories as $mainCategory) {
        //     $this->ocsService->syncVehicleModels($mainCategory->descendants, $mainCategory->vehicles);
        // }

    }
}
