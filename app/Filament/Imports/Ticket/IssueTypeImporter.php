<?php

namespace App\Filament\Imports\TS;

use App\Models\TS\Issue\Type as IssueType;
use App\Models\TS\StandardisedActivityGroup;
use App\Models\TS\Task\TemplateGroup;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class IssueTypeImporter extends Importer
{
    protected static ?string $model = IssueType::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return "";
                    }

                    return Str::trim($state);
                }),
            ImportColumn::make('description')
                ->rules(['max:255']),
            ImportColumn::make('parent')
                ->relationship('parent', 'title')
                // ->rules(['required', 'max:255']),
                ->rules(['max:255']),                                       
            ImportColumn::make('department_group')
                ->relationship('departmentGroup', 'code')
                // ->rules(['required', 'max:255']),
                ->rules(['max:255']),                  
        ];
    }

    public function resolveRecord(): ?IssueType
    {
        // return ShiftTemplate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new IssueType();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your issue type import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
