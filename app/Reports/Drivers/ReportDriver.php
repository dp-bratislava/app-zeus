<?php

namespace App\Reports\Drivers;

use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;

interface ReportDriver
{
    /**
     * Get the unique key for this driver (used in query parameters and tabs)
     */
    public function key(): string;

    /**
     * Get the human-readable name for this driver
     */
    public function name(): string;

    /**
     * Permission required to view this report.
     * Return null for publicly accessible reports (no restriction).
     */
    public function permission(): ?string;

    public function getQuery(): Builder;

    /**
     * Get the columns configuration for the table
     */
    public function getColumns(): array;

    /**
     * Get the filters configuration for the table
     */
    public function getFilters(): array;

    public function getSortColumn(): string;

    public function getSortOrder(): string;

    public function getExporter(): string;
    
    public function generateExportFilename(): string;

    /**
     * Apply additional query modifications if needed (for relationships, eager loading, etc.)
     */
    public function applyQueryModifications(Builder $query): Builder;

    public function lastSyncedAt(): ?string;

}
