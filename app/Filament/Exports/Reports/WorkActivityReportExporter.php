<?php

namespace App\Filament\Exports\Reports;

use App\Models\Reports\Export;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WorkActivityReportExporter
{
    public function generate(array $filters, string $fileName, $userId)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // build header columns
        $columns = $this->columns();
        $headers = array_map(fn($col) => $col['label'], $columns);

        $sheet->fromArray([$headers], null, 'A1');
        $rowIndex = 2;

        // get query
        $query = $this->query($filters);

        // proces query
        $query->chunkById(2000, function ($rows) use ($sheet, &$rowIndex) {
            $data = [];

            // map rows
            foreach ($rows as $row) {
                $data[] = [
                    $row->department_code,
                    $row->task_created_at,
                    $row->task_date,
                    $row->subject_code,
                    $row->task_group_title,
                    $row->task_maintenance_group_code,
                    $row->task_author_lastname,
                    $row->task_item_group_title,
                    $row->task_item_maintenance_group_code,
                    $row->task_item_author_lastname,
                    $row->wtf_task_created_at,
                    $row->activity_date,
                    $row->personal_id,
                    $row->last_name,
                    $row->first_name,
                    $row->wtf_task_title,
                    $row->expected_duration < 0
                        ? ($row->real_duration / 86400)
                        : ($row->expected_duration / 86400),
                    $row->real_duration < 0
                        ? 0
                        : ($row->real_duration / 86400),
                    match ($row->is_fulfilled) {
                        0 => 'Nie',
                        1 => 'Áno',
                        default => 'Nevyhodnotené',
                    },
                ];
            }

            $sheet->fromArray($data, null, 'A' . $rowIndex);

            $rowIndex += count($data);
        });

        // aply styles
        $this->applyStyles($sheet);

        // get file path         
        $fullPath = Storage::disk('report-exports')->path($fileName);        

        $writer = new Xlsx($spreadsheet);
        $writer->save($fullPath);

        // store export metadata in db
        return Export::create([
            'completed_at' => now(),
            'exporter' => '',
            'file_disk' => 'report-exports',
            'file_name' => pathinfo($fullPath, PATHINFO_FILENAME),
            'processed_rows' => 0,
            'successful_rows' => $query->count(),
            'total_rows' => $query->count(),
            'user_id' => $userId,
        ]);
    }

    private function query(array $filters)
    {
        $departmentIds = data_get($filters, 'department.values');

        return DB::table('mvw_work_activity_report')
            ->select(['id', ...array_column($this->columns(), 'key')])
            ->when(data_get($filters, 'activity_date.activity_date_from'), fn($q, $v) => $q->whereDate('activity_date', '>=', $v))
            ->when(data_get($filters, 'activity_date.activity_date_to'), fn($q, $v) => $q->whereDate('activity_date', '<=', $v))
            ->when(!empty($departmentIds), function ($q) use ($departmentIds) {
                $q->whereIn('department_id', $departmentIds);
            });
    }

    private function columns(): array
    {
        return [
            ['key' => 'department_code', 'label' => 'Stredisko'],
            ['key' => 'task_created_at', 'label' => 'Čas vytvorenia zákazky'],
            ['key' => 'task_date', 'label' => 'Dátum zákazky', 'type' => 'date'],
            ['key' => 'subject_code', 'label' => 'Vozidlo'],
            ['key' => 'task_group_title', 'label' => 'Typ zákazky'],
            ['key' => 'task_maintenance_group_code', 'label' => 'Prevádzka zákazky'],
            ['key' => 'task_author_lastname', 'label' => 'Zákzaku vytvoril'],
            ['key' => 'task_item_group_title', 'label' => 'Typ podzákazky'],
            ['key' => 'task_item_maintenance_group_code', 'label' => 'Prevádzka podzákazky'],
            ['key' => 'task_item_author_lastname', 'label' => 'Podzákazku vytvoril'],
            ['key' => 'wtf_task_created_at', 'label' => 'Čas priradenia práce'],
            ['key' => 'activity_date', 'label' => 'Dátum výkonu práce', 'type' => 'date'],
            ['key' => 'personal_id', 'label' => 'Osobné číslo'],
            ['key' => 'last_name', 'label' => 'Priezvisko'],
            ['key' => 'first_name', 'label' => 'Meno'],
            ['key' => 'wtf_task_title', 'label' => 'Norma'],
            ['key' => 'expected_duration', 'label' => 'Norma trvanie', 'type' => 'duration'],
            ['key' => 'real_duration', 'label' => 'Reálne trvanie', 'type' => 'duration'],
            ['key' => 'is_fulfilled', 'label' => 'Splnené', 'type' => 'bool'],
        ];
    }

    private function applyStyles(Worksheet $sheet)
    {
        $sheet->freezePane('A2');

        // make header bold
        $lastColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        // adjust column index for expected_duration
        $sheet->getStyle('Q:Q')
            ->getNumberFormat()
            ->setFormatCode('[h]:mm');
        // real_duration
        $sheet->getStyle('R:R')
            ->getNumberFormat()
            ->setFormatCode('[h]:mm');

        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->setAutoFilter(
            $sheet->calculateWorksheetDimension()
        );
    }
}
