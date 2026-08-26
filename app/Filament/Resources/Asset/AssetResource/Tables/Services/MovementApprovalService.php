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

    public static function approveMovements(Collection $records): void
    {
        self::authorizeApproval();

        [$approvedCount, $skippedCount] = self::processMovements(
            $records,
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

    public static function rejectMovements(Collection $records): void
    {
        self::authorizeApproval();

        [$skippedCount, $rejectedCount] = self::processMovements(
            $records,
            ApprovalStatus::REJECTED
        );

        if ($skippedCount > 0) {
            Notification::make()
                ->title('Schválené (nie je potrebné / už schválené): ' . $skippedCount)
                ->success()
                ->send();
        }

        Notification::make()
            ->title('Zamietnuté pohyby: ' . $rejectedCount)
            ->danger()
            ->send();
    }

    public static function resetMovementsToPending(Collection $records): void
    {
        self::authorizeApproval();

        [$processedCount, $skippedCount] = self::processMovements(
            $records,
            ApprovalStatus::PENDING
        );

        if ($skippedCount > 0) {
            Notification::make()
                ->title('Nepospracované (neboli schválené / zamietnuté): ' . $skippedCount)
                ->info()
                ->send();
        }

        Notification::make()
            ->title('Vrátené do schválenia: ' . $processedCount)
            ->success()
            ->send();
    }

    protected static function processMovements(
        Collection $records,
        ApprovalStatus $targetStatus
    ): array {
        $processedCount = 0;
        $skippedCount = 0;

        foreach ($records as $asset) {
            if (!$asset instanceof Asset) {
                $skippedCount++;
                continue;
            }

            // For pending reset, skip assets that don't have approval history
            if ($targetStatus === ApprovalStatus::PENDING) {
                if (!self::canResetToPending($asset)) {
                    $skippedCount++;
                    continue;
                }
            } else {
                // For approve/reject, skip assets not waiting for approval
                if (!$asset->waitingForApproval()) {
                    $skippedCount++;
                    continue;
                }
            }

            $movement = $asset->latestMovement;
            if (!$movement) {
                $skippedCount++;
                continue;
            }

            self::createApprovalRecord($movement, $targetStatus);
            $processedCount++;
        }

        return [$processedCount, $skippedCount];
    }

    protected static function canResetToPending(Asset $asset): bool
    {
        $movement = $asset->latestMovement;
        if (!$movement) {
            return false;
        }

        $status = $movement->getApprovalStatus();
        return $status === ApprovalStatus::APPROVED || $status === ApprovalStatus::REJECTED;
    }

    protected static function createApprovalRecord($movement, ApprovalStatus $status): void
    {
        $movement->approval()->create([
            'status' => $status,
            'actioned_by' => Auth::id(),
            'actioned_at' => now(),
        ]);
    }
}