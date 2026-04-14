<?php

namespace App\Filament\Exports\Reports;

use App\Models\Reports\WorkActivityReport;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Carbon;
use OpenSpout\Common\Entity\Style\Style;

class WorkActivityReportExporter extends Exporter
{
    protected static ?string $model = WorkActivityReport::class;

    public static function getColumns(): array
    {
        return [
            // ExportColumn::make('id')
            //     ->label('ID'),
            // ExportColumn::make('department_id'),
            ExportColumn::make('department_code')->label('Stredisko'),
            ExportColumn::make('task_created_at')->label('Čas vytvorenia zakázky'),
            ExportColumn::make('task_date')
                ->label('Dátum zakázky'),
                // ->formatStateUsing(function ($state) {
                //     return $state === null ? Carbon::parse($state)->format('Y-m-d') : null;
                // }),
            ExportColumn::make('subject_code')->label('Vozidlo'),
            ExportColumn::make('task_group_title')->label('Typ zakázky'),
            ExportColumn::make('task_maintenance_group_code')->label('Prevádzka zakázky'),
            ExportColumn::make('task_author_lastname')->label('Zakázku vytvoril'),
            ExportColumn::make('task_item_group_title')->label('Typ podzakázky'),
            ExportColumn::make('task_item_maintenance_group_code')->label('Prevádzka podzakázky'),
            ExportColumn::make('task_item_author_lastname')->label('Podzakázku vytvoril'),
            ExportColumn::make('wtf_task_created_at')->label('Čas priradenia práce'),
            ExportColumn::make('activity_date')
                ->label('Dátum výkonu práce'),
                // ->formatStateUsing(function ($state) {
                //     return Carbon::parse($state)->format('Y-m-d');
                // }),
            ExportColumn::make('personal_id')->label('Osobné číslo'),
            ExportColumn::make('last_name')->label('Priezvisko'),
            ExportColumn::make('first_name')->label('Meno'),
            ExportColumn::make('wtf_task_title')->label('Norma'),
            ExportColumn::make('expected_duration')
                ->label('Norma trvanie')
                ->formatStateUsing(function ($state) {
                    return ($state < 0) ? 0.0 : ($state / 86400);
                }),
            ExportColumn::make('real_duration')
                ->label('Reálne trvanie')
                ->formatStateUsing(function ($state) {
                    return $state / 86400;
                }),
            ExportColumn::make('is_fulfilled')
                ->label('Splnené')
                ->formatStateUsing(function ($state) {
                    return match ($state) {
                        0 => 'Nie',
                        1 => 'Áno',
                        default => 'Nevyhodnotené'
                    };
                }),
        ];
    }

    public function getXlsxCellStyle(): ?Style
    {
        return (new Style())
            ->setShouldWrapText();
    }

public function getXlsxOptions(): ?Options
{
    $options = new Options();
    
    // Set fixed width for specific columns
    // Arguments: (width, startColumnIndex, endColumnIndex)
    // Note: Column index starts at 1
    $options->setColumnWidth(25, 1); // Set first column to width 25
    $options->setColumnWidth(40, 2); // Set second column to width 40
    $options->setColumnWidth(15, 3, 5); // Set columns 3, 4, and 5 to width 15
    
    return $options;
}


    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your work activity report export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
