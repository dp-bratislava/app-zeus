<?php

namespace Database\Seeders\LocalDev;

use Dpb\Package\Assets\Models\Asset;
use Dpb\Package\Assets\Models\AssetMovement;
use Dpb\Package\Assets\Models\AssetSlot;
use Dpb\Package\Assets\Models\AssetTemplate;
use Dpb\Package\Assets\Models\AssetType;
use Dpb\Package\Assets\Enums\AssetMovementType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Local dev sample data for the "zariadenia" feature: a handful of concrete parts and
 * their montáž / demontáž movements on real vehicles, so the Zariadenia / Pohyby lists
 * and the vehicle/part overview are populated to click around in.
 *
 * Idempotent: all sample rows use the "VZ-" serial prefix and are wiped + recreated on
 * each run. To remove them entirely: delete assets where serial_number like 'VZ-%'
 * (their movements are removed with them).
 *
 * Run: ./vendor/bin/sail artisan db:seed --class="Database\Seeders\LocalDev\SampleAssetSeeder"
 */
class SampleAssetSeeder extends Seeder
{
    private const SERIAL_PREFIX = 'VZ-';

    /** How many vehicles to populate. */
    private const SAMPLE_VEHICLES = 6;

    /** One representative template title per asset-type code. */
    private const TEMPLATE_TITLES = [
        'pneumatiky' => 'Pneumatika 295/80 R22.5',
        'motor' => 'Motor Cummins ISL9',
        'akumulator' => 'Batéria 12V 225Ah',
    ];

    public function run(): void
    {
        $this->wipeExisting();

        $templates = $this->ensureTemplates();

        $vehicles = $this->sampleVehicles();
        if ($vehicles->isEmpty()) {
            $this->command?->warn('No vehicles with slots found — run AssetSlotSeeder first.');

            return;
        }

        $serial = 0;
        $assets = 0;
        $movements = 0;

        foreach ($vehicles as $i => $vehicle) {
            $slots = AssetSlot::query()
                ->where('fleet_vehicle_model_id', $vehicle->model_id)
                ->orderBy('asset_type_id')
                ->orderBy('sort_order')
                ->get();

            foreach ($slots as $slotIndex => $slot) {
                $templateId = $templates[$slot->asset_type_id] ?? null;

                // Install the currently-fitted part.
                $current = Asset::create([
                    'serial_number' => self::SERIAL_PREFIX . str_pad((string) ++$serial, 5, '0', STR_PAD_LEFT),
                    'template_id' => $templateId,
                ]);
                $assets++;

                // On the first tyre slot of the first couple of vehicles, add a prior
                // worn part that was demounted, so the history shows a demontáž → montáž.
                if ($i < 2 && $slotIndex === 0) {
                    $old = Asset::create([
                        'serial_number' => self::SERIAL_PREFIX . str_pad((string) ++$serial, 5, '0', STR_PAD_LEFT),
                        'template_id' => $templateId,
                    ]);
                    $assets++;

                    $this->move($old, $slot, $vehicle->id, AssetMovementType::INSTALLED, now()->subMonths(8), 'Pôvodný diel');
                    $this->move($old, $slot, $vehicle->id, AssetMovementType::REMOVED, now()->subMonths(1), 'Opotrebovanie');
                    $this->move($current, $slot, $vehicle->id, AssetMovementType::INSTALLED, now()->subMonths(1), 'Výmena za nový');
                    $movements += 3;

                    continue;
                }

                $this->move($current, $slot, $vehicle->id, AssetMovementType::INSTALLED, now()->subMonths(6 - ($i % 5)), 'Montáž');
                $movements++;
            }
        }

        $this->command?->info(sprintf(
            'Sample data: %d assets and %d movements across %d vehicle(s).',
            $assets,
            $movements,
            $vehicles->count(),
        ));
    }

    private function move(Asset $asset, AssetSlot $slot, int $vehicleId, AssetMovementType $type, \DateTimeInterface $date, string $reason): void
    {
        AssetMovement::create([
            'asset_id' => $asset->id,
            'slot_id' => $slot->id,
            'vehicle_id' => $vehicleId,
            'movement_type' => $type->value,
            'date' => $date,
            'reason' => $reason,
        ]);
    }

    /** One AssetTemplate per asset type, keyed by asset_type_id. */
    private function ensureTemplates(): array
    {
        $map = [];

        foreach (self::TEMPLATE_TITLES as $code => $title) {
            $type = AssetType::where('code', $code)->first();
            if (! $type) {
                continue;
            }

            $template = AssetTemplate::firstOrCreate(
                ['title' => $title, 'type_id' => $type->id],
            );
            $map[$type->id] = $template->id;
        }

        return $map;
    }

    /** A spread of vehicles whose model has slots defined. */
    private function sampleVehicles()
    {
        $modelIds = AssetSlot::query()->distinct()->pluck('fleet_vehicle_model_id');

        return DB::table('fleet_vehicles')
            ->whereIn('model_id', $modelIds)
            ->orderBy('id')
            ->limit(self::SAMPLE_VEHICLES)
            ->get(['id', 'model_id']);
    }

    private function wipeExisting(): void
    {
        $ids = Asset::where('serial_number', 'like', self::SERIAL_PREFIX . '%')->pluck('id');

        if ($ids->isNotEmpty()) {
            AssetMovement::whereIn('asset_id', $ids)->forceDelete();
            Asset::whereIn('id', $ids)->forceDelete();
        }
    }
}
