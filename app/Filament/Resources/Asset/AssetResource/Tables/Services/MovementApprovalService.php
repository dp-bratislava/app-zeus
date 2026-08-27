<?php

namespace App\Filament\Resources\Asset\AssetResource\Tables\Services;

use Dpb\Package\Assets\Enums\ApprovalStatus;
use Dpb\WtfTmsBridge\Models\Asset;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Dpb\DpbUtils\Helpers\UserPermissionHelper;

class MovementApprovalService
{
    public static function authorizeApproval(): void
    {
        abort_unless(
            UserPermissionHelper::hasPermission('pkg-assets.asset-movement.approve'),
            403,
        );
    }

    public static function approveMovements(Collection $records, array $data): void
    {
        self::authorizeApproval();

        [$approvedCount, $skippedCount] = self::processMovements(
            $records,
            $data,
            ApprovalStatus::APPROVED
        );

        if ($skippedCount > 0) {
            Notification::make()
                ->title('Neschválené (nie je potrebné / už schválené): ' . $skippedCount)
                ->warning()
                ->send();
        }

        Notification::make()
            ->title('Schválené pohyby: ' . $approvedCount)
            ->success()
            ->send();
    }

    public static function rejectMovements(Collection $records, array $data): void
    {
        self::authorizeApproval();
        self::processMovements(
            $records,
            $data,
            ApprovalStatus::REJECTED
        );
    }

    public static function postponeMovements(Collection $records, array $data): void
    {
        self::authorizeApproval();
        self::processMovements(
            $records,
            $data,
            ApprovalStatus::PENDING
        );
    }

    public static function resetMovementsToPending(Collection $records, array $data = []): void
    {
        self::authorizeApproval();
        self::processMovements(
            $records,
            $data,
            ApprovalStatus::PENDING
        );
    }

    protected static function processMovements(
        Collection $records,
        array $data,
        ApprovalStatus $targetStatus
    ): array {
        $processedCount = 0;
        $skippedCount = 0;

        $movementsData = collect($data['movements'] ?? [])
            ->keyBy('movement_id');

        foreach ($records as $asset) {
            $movement = $asset->latestMovement;
            if (! $movement) {
                $skippedCount++;
                continue;
            }

            $currentStatus = $movement->getApprovalStatus();
            if ($currentStatus === $targetStatus) {
                $skippedCount++;
                continue;
            }

            $row = $movementsData->get($movement->id);
            $comment = $row['comment'] ?? null;

            self::createApprovalRecord($movement, $targetStatus, $comment);
            $processedCount++;
        }

        return [$processedCount, $skippedCount];
    }

    protected static function createApprovalRecord($movement, ApprovalStatus $status, ?string $comment = null): void
    {
        $movement->approval()->create([
            'status' => $status,
            'comment' => $comment,
            'actioned_by' => Auth::id(),
            'actioned_at' => now(),
        ]);
    }
}