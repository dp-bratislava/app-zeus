<?php

namespace App\Reports\Concerns;

use Illuminate\Support\Facades\Gate;

trait HasReportPermission
{
    /**
     * Permission required to view this report.
     *
     * Returns null by default, meaning the report is publicly accessible.
     * Override in a driver to restrict it
     */
    public function permission(): ?string
    {
        return null;
    }

    /**
     * Whether the given (or currently authenticated) user may view this report.
     */
    public function isAccessible(?string $permission = null): bool
    {
        $permission ??= $this->permission();

        return $permission === null || Gate::allows($permission);
    }
}
