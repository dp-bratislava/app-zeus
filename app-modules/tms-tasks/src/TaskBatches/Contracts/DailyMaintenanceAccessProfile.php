<?php

namespace Dpb\Modules\Tasks\Contracts;

use Dpb\Modules\Tasks\Context\DailyMaintenanceContext;

interface DailyMaintenanceAccessProfile
{
    public function canViewAny(DailyMaintenanceContext $context): bool;
    public function canView(DailyMaintenanceContext $context): bool;
    public function canCreate(DailyMaintenanceContext $context): bool;
    public function canUpdate(DailyMaintenanceContext $context): bool;
    public function canDelete(DailyMaintenanceContext $context): bool;
}