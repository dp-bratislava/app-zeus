<?php

namespace App\Filament\Imports\WTF;

use App\Models\WTF\ActivityTemplate;
use App\Models\WTF\ActivityGroup;
use App\Models\WTF\ActivityType;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ActivityTemplateImporter extends Importer
{
    protected static ?string $model = ActivityTemplate::class;

    public function __construct(
        protected Import $import,
        protected array $columnMap,
        protected array $options,
        protected Collection $groups,
        protected Collection $types,
    ) {
        $this->groups = ActivityGroup::all()->pluck('id', 'title');
        $this->types = ActivityType::all()->pluck('id', 'code');
    }

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
                // ->requiredMapping()
                ->rules(['max:255'])
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return "";
                    }

                    return Str::trim($state);
                }),                
            ImportColumn::make('group')
                ->relationship('group', 'title')
                ->rules(['required', 'max:255']),
            ImportColumn::make('type')
                ->relationship('type', 'code')
                ->rules(['required', 'max:255']),                                
        ];
    }

    public function resolveRecord(): ?ActivityTemplate
    {
        // return ShiftTemplate::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new ActivityTemplate();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your shift template import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
