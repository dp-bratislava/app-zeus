<?php

namespace Database\Seeders\LocalDev;

use Dpb\Package\Assets\Models\AssetSlot;
use Dpb\Package\Assets\Models\AssetType;
use Dpb\Package\Fleet\Models\VehicleModel;
use Dpb\Package\Tasks\Models\TaskItemGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Local dev seeder for the "zariadenia" (asset movements) feature.
 *
 * Ensures the three device asset types, maps every matching task-item-group variant
 * (the same device subgroup exists once per top-level task group) to its type so the
 * tab shows regardless of which variant a podzákazka uses, and generates a sensible set
 * of slots per vehicle model based on the vehicle type.
 *
 * Slot strategy (heuristic — refine oddballs via the AssetSlot admin):
 *   - Pneumatiky: bus/trolleybus 6 (8 if articulated), car 4, truck 6, tram 0.
 *   - Motor: 1 for every powered road vehicle (bus/trolleybus/car/truck), tram 0.
 *   - Akumulátor: bus/trolleybus/truck 2, car 1, tram 0.
 * Trams (Električka) are skipped — rail vehicles, a separate maintenance domain.
 *
 * Run: ./vendor/bin/sail artisan db:seed --class="Database\Seeders\LocalDev\AssetSlotSeeder"
 */
class AssetSlotSeeder extends Seeder
{
    private const TYPE_AUTOBUS = 1;
    private const TYPE_ELEKTRICKA = 2; // tram — skipped
    private const TYPE_TROLEJBUS = 3;
    private const TYPE_OSOBNE = 4;     // car
    private const TYPE_NAKLADNE = 5;   // truck

    public function run(): void
    {
        $pneumatiky = AssetType::firstOrCreate(['code' => 'pneumatiky'], ['title' => 'Pneumatiky']);
        $motor = AssetType::firstOrCreate(['code' => 'motor'], ['title' => 'Motor']);
        $akumulator = AssetType::firstOrCreate(['code' => 'akumulator'], ['title' => 'Akumulátor']);

        // Map every task-item-group variant (by title) to its device type.
        $this->mapGroups('Pneumatiky, defekt kolesa', $pneumatiky);
        $this->mapGroups('Spaľovací motor', $motor);
        $this->mapGroups('Elektrická sústava (12v/24 v)', $akumulator);

        $models = VehicleModel::query()->whereHas('vehicles')->get();

        if ($models->isEmpty()) {
            $this->command?->warn('No vehicle models with existing vehicles found — skipping asset slot seed.');

            return;
        }

        $created = 0;

        foreach ($models as $model) {
            $created += $this->makeSlots($model->id, $pneumatiky, $this->tyreCount($model), 'P', 'Pneumatika');
            $created += $this->makeSlots($model->id, $motor, $this->motorCount($model), 'M', 'Motor');
            $created += $this->makeSlots($model->id, $akumulator, $this->batteryCount($model), 'B', 'Akumulátor');
        }

        $this->command?->info(sprintf(
            'Asset slots: %d created across %d vehicle model(s).',
            $created,
            $models->count(),
        ));
    }

    private function tyreCount(VehicleModel $model): int
    {
        return match ((int) $model->type_id) {
            self::TYPE_ELEKTRICKA => 0,
            self::TYPE_OSOBNE => 4,
            self::TYPE_AUTOBUS, self::TYPE_TROLEJBUS => $this->isArticulated($model) ? 8 : 6,
            default => 6, // truck and anything else
        };
    }

    private function motorCount(VehicleModel $model): int
    {
        return (int) $model->type_id === self::TYPE_ELEKTRICKA ? 0 : 1;
    }

    private function batteryCount(VehicleModel $model): int
    {
        return match ((int) $model->type_id) {
            self::TYPE_ELEKTRICKA => 0,
            self::TYPE_OSOBNE => 1,
            default => 2,
        };
    }

    /** Articulated (3-axle) buses/trolleybuses, detected from the model title. */
    private function isArticulated(VehicleModel $model): bool
    {
        $title = mb_strtolower((string) $model->title);

        return (bool) preg_match('/\b18\b/', $title)
            || str_contains($title, 'articul')
            || str_contains($title, 'capacity')
            || str_contains($title, 'ikarus 28'); // 280 / 283 are articulated
    }

    /**
     * Create position slots P1..Pn for a model + asset type. Idempotent.
     *
     * @return int number of slots newly created
     */
    private function makeSlots(int $modelId, AssetType $type, int $count, string $prefix, string $labelStem): int
    {
        $created = 0;

        for ($i = 1; $i <= $count; $i++) {
            $code = $prefix . $i;

            $slot = AssetSlot::firstOrCreate(
                [
                    'fleet_vehicle_model_id' => $modelId,
                    'asset_type_id' => $type->id,
                    'position_code' => $code,
                ],
                [
                    'label' => $count === 1 ? "{$labelStem} ({$code})" : "{$labelStem} {$i} ({$code})",
                    'sort_order' => $i,
                ],
            );

            $created += $slot->wasRecentlyCreated ? 1 : 0;
        }

        return $created;
    }

    /** Point every group sharing this title at the asset type (asset_type_id is not fillable). */
    private function mapGroups(string $title, AssetType $type): void
    {
        /** @var Collection<int, TaskItemGroup> $groups */
        $groups = TaskItemGroup::query()->where('title', $title)->get();

        $groups->each(fn (TaskItemGroup $group) => $group->forceFill(['asset_type_id' => $type->id])->save());

        $this->command?->info(sprintf(
            'Mapped %d group(s) "%s" → asset type "%s".',
            $groups->count(),
            $title,
            $type->title,
        ));
    }
}
