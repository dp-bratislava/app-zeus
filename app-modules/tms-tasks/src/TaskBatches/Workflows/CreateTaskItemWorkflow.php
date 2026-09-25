<?php

namespace Dpb\Modules\Tasks\Workflows;

use Dpb\Package\Assets\Enums\MorphType;
use Dpb\\Asset;
use Dpb\\AssetMovement;
use Dpb\Package\Assets\Models\AssetSlot;
use Dpb\Package\Assets\Models\SerialNumber;
use Dpb\Package\TaskMS\Commands\TaskItem\CreateTaskItemCommand;
use Dpb\Package\TaskMS\Commands\TaskItemAssignment\CreateTaskItemAssignmentCommand;
use Dpb\Package\TaskMS\Handlers\TaskItem\CreateTaskItemHandler;
use Dpb\Package\TaskMS\Handlers\TaskItemAssignment\CreateTaskItemAssignmentHandler;
use Dpb\Package\TaskMS\States;
use Dpb\Modules\Tasks\Enums\AssetState;
use Dpb\package\Assets\Enums\ApprovalStatus;
use Dpb\Modules\Tasks\Enums\MovementType;
use Dpb\Modules\Tasks\Filament\Resources\Task\TaskAssignmentResource\Forms\Data\MovementRow;
use Dpb\Modules\Tasks\Filament\Resources\Task\TaskAssignmentResource\Forms\Support\MovementStaging;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Illuminate\Support\now;

class CreateTaskItemWorkflow
{
    public function __construct(
        private CreateTaskItemHandler $tiCHdl,
        private CreateTaskItemAssignmentHandler $tiaCHdl,
    ) {}

    public function execute(int $taskId, array $data): Model
    {
        return DB::transaction(function () use ($taskId, $data) {
            $taskItem = $this->tiCHdl->handle(
                new CreateTaskItemCommand(
                    new \DateTimeImmutable($data['date']),
                    $taskId,
                    null,
                    $data['description'] ?? null,
                    States\Task\TaskItem\Created::$name,
                    $data['group_id']
                )
            );

            $this->attachSlotsToTaskItem($taskItem, $data);
            $this->stageMovements($taskItem, $data['movements'] ?? []);

            return $this->tiaCHdl->handle(
                new CreateTaskItemAssignmentCommand(
                    $taskItem->id,
                    $data['assigned_to'] ?? null,
                    isset($data['assigned_to']) ? 'maintenance-group' : null,
                    Auth::user()->id,
                )
            );
        });
    }
    
    private function attachSlotsToTaskItem($taskItem, array $data): void
    {
        $assetSlots = $data['asset-slots'] ?? [];
        if(!empty($assetSlots)) 
        {
            $taskItem->assetSlots()->attach($assetSlots);
        }
    }

    private function stageMovements(Model $taskItem, array $movementStates): void
    {
        foreach ($movementStates as $movementState) {
            $movement = MovementRow::fromState($movementState);
            // A movement type is mandatory.
            if ($movement->movementType === null) {
                continue;
            }

            // When "no serial number" is selected, create the corresponding asset
            // (with a null serial number) so the movement still references an asset.
            if ($movement->assetId === MovementStaging::NO_SERIAL_VALUE) {
                $movement = new MovementRow(
                    slotId: $movement->slotId,
                    locationToLabel: $movement->locationToLabel,
                    movementType: $movement->movementType,
                    assetId: $this->createSeriallessAsset($movement->slotId),
                    vehicleId: $movement->vehicleId,
                    kilometrage: $movement->kilometrage,
                    newState: $movement->newState,
                    previousAsset: $movement->previousAsset,
                    approvalStatus: $movement->approvalStatus,
                );
            }

            $this->applyMovement($taskItem, $movement);
        }
    }

    private function createSeriallessAsset(?int $slotId): int
    {
        $typeId = $slotId ? AssetSlot::find($slotId)?->asset_type_id : null;

        $asset = Asset::create(['type_id' => $typeId]);

        // No serial number was provided – assign a virtual one so the asset
        // and its movements always have a serial number reference.
        SerialNumber::resolveOrCreateVirtual($asset);

        return $asset->id;
    }

    private function applyMovement(Model $taskItem, MovementRow $movement): void
    {
        $movementType = MovementType::tryFrom($movement->movementType);

        [$locationFromType, $locationFromId] = $this->resolveLocationFrom($movement, $movementType);

        $isMontaz = $movementType === MovementType::MONTAZ;

        $approvalStatus = $movement->approvalStatus;
        $newState = $movement->newState;
        if($newState->requiresApproval()) {
            $approvalStatus = ApprovalStatus::PENDING;
        }
        
        $createdMovement = $taskItem->assetMovements()->create([
            'date' => now()->toDateString(),
            'movement_type' => $movementType,
            'state_result' => $movement->newState,
            'asset_id' => $movement->assetId,
            'vehicle_id' => $movement->vehicleId,
            'location_from_type' => $locationFromType,
            'location_from_id' => $locationFromId,
            'location_to_type' => $isMontaz ? MorphType::SLOT->key() : null,
            'location_to_id' => $isMontaz ? $movement->slotId : null,
            'kilometrage' => $movement->kilometrage ?? 0,
            'reason' => 'Pri vytvarani podzakazky',
            'performed_by' => Auth::user()->id,
        ]);

        if ($approvalStatus !== ApprovalStatus::NOT_REQUIRED) {
            $createdMovement->approval()->create([
                'status' => $approvalStatus,
                'requested_by' => Auth::user()->id,
                'actioned_at' => null,
                'comment' => null,
            ]);
        }
    }

    private function resolveLocationFrom(MovementRow $movement, ?MovementType $movementType): array
    {
        $lastMovement = AssetMovement::query()
            ->where('asset_id', $movement->assetId)
            ->orderByDesc('id')
            ->first();

        $previousLocationToType = $lastMovement?->location_to_type;
        $previousLocationToId = $lastMovement?->location_to_id;

        $hasPreviousLocation = $previousLocationToType && $previousLocationToId;
        $isDemontaz = $movementType === MovementType::DEMONTAZ;

        if ($hasPreviousLocation) {
            return [$previousLocationToType, $previousLocationToId];
        }

        return $isDemontaz
            ? [MorphType::SLOT->key(), $movement->slotId]
            : [null, null];
    }
}