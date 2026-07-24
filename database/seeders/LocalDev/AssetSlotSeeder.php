<?php

namespace Database\Seeders\LocalDev;

use Dpb\Package\Assets\Models\AssetSlot;
use Dpb\Package\Assets\Models\AssetType;
use Dpb\WtfTmsBridge\Enums\AssetState;
use Dpb\Package\Assets\Models\Asset;
use Dpb\Package\Fleet\Models\VehicleModel;
use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Database\Seeder;
use Dpb\WtfTmsBridge\Models\AssetTypeTaskItemGroup;
use Dpb\Package\Tasks\Models\TaskItemGroup;

/**
 * Run: ./vendor/bin/sail artisan db:seed --class="Database\Seeders\LocalDev\AssetSlotSeeder"
 */
class AssetSlotSeeder extends Seeder
{
    public function run(): void
{
    $assetTypes = $this->getOrCreateAssetTypes();

    // Fetch all vehicle models of type 1
    $busVehicleModels = VehicleModel::where('type_id', 1)->get();
    $onVehicleState = AssetState::DIEL_NA_VOZE;
    $id = 0;
    $assetsToInsert = [];
    $slotsToInsert = [];

    foreach ($busVehicleModels as $busVehicleModel) {
        // Determine counts for this specific vehicle model
        $counts = $this->getSlotCountsPerType($assetTypes);

        // Fetch all vehicles for this model at once to avoid N+1 queries
        $vehicles = Vehicle::where('model_id', $busVehicleModel->id)->get();
        
        $now = now();

        foreach ($vehicles as $vehicle) {
            foreach ($counts as $assetTypeId => $count) {
                for ($i = 1; $i <= $count; $i++) {
                    $id++;
                    $slotsToInsert[] = [
                        'id'            => $id,
                        'vehicle_id'    => $vehicle->id,
                        'asset_type_id' => $assetTypeId,
                        'order_number'  => $i,
                        'section'       => 'A',
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];

                    if (rand(0, 1) === 1) {
                        $assetsToInsert[] = [
                            'id'            => $id,
                            'serial_number' => 'SN-' . strtoupper(uniqid()),
                            'type_id' => $assetTypeId,
                            'kilometrage' => rand(0, 100000),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        // MOVED INSIDE THE LOOP: Batch insert data for this specific model, then free memory
        if (!empty($slotsToInsert)) {
            AssetSlot::insertOrIgnore($slotsToInsert);
            $slotsToInsert = []; // Reset array so the next model starts fresh
        }

        if (!empty($assetsToInsert)) {
            Asset::insertOrIgnore($assetsToInsert);
            $assetsToInsert = []; // Reset array so the next model starts fresh
        }
    }
}

    /**
     * Retrieves or creates the required AssetTypes, returning a map of code => id.
     */
    private function getOrCreateAssetTypes(): array
    {
        $typesData = [
            'pneumatiky'       => 'Pneumatiky',
            'motor'            => 'Motor',
            'dvere'            => 'Dvere',
            'brzdy'            => 'Brzdy',
            'prevodovka'       => 'Prevodovka',
            'klimatizacia'     => 'Klimatizácia',
            'palubny_pocitac'  => 'Palubný počítač',
        ];

        $mappedTypes = [];
        foreach ($typesData as $code => $title) {
            $type = AssetType::firstOrCreate(['code' => $code], ['title' => $title]);
            $mappedTypes[$code] = $type->id;
        }

        $this->createAssetTypeTaskItemGroup($mappedTypes);

        return $mappedTypes;
    }

    /**
     * Generates the randomized and static slot counts mapped by AssetType ID.
     */
    private function getSlotCountsPerType(array $assetTypes): array
    {
        return [
            $assetTypes['pneumatiky']       => rand(2, 7) * 2,
            $assetTypes['motor']            => rand(1, 3),
            $assetTypes['dvere']            => rand(2, 5),
            $assetTypes['brzdy']            => rand(1, 3),
            $assetTypes['prevodovka']       => 1,
            $assetTypes['klimatizacia']     => 1,
            $assetTypes['palubny_pocitac']  => 1,
        ];
    }

    private function createAssetTypeTaskItemGroup(array $mappedTypes): void
    {

        $assetTypeTaskItemGroup = [
            'motor' => 'Spaľovací motor',
            'prevodovka' => 'Prevodovka',
            'dvere' => 'Dvere',
            'brzdy' => 'Brzdový systém',
            'klimatizacia' => 'Klimatizácia',
            'pneumatiky' => 'Pneumatiky, defekt kolesa',
            'palubny_pocitac' => 'Programovanie',
        ];

        foreach ($mappedTypes as $code => $typeId) {
            $taskGroupIds = TaskItemGroup::where('title', $assetTypeTaskItemGroup[$code] ?? '')->pluck('id')->toArray();
            foreach ($taskGroupIds as $taskGroupId) {
                AssetTypeTaskItemGroup::firstOrCreate(
                    ['asset_type_id' => $typeId, 'task_item_group_id' => $taskGroupId]
                );
            }
        }
    }
}