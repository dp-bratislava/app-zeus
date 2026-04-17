<?php

namespace App\Filament\Exports\Reports;

use App\Models\Reports\WorkActivityReport;
use Illuminate\Support\Carbon;
use OpenSpout\Common\Entity\Row;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use OpenSpout\Writer\XLSX\Writer;

class CustomWorkActivityReportExporter
{
    public function stream(array $filters, string $fileName): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // dd($filters);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $columns = $this->columns();
        $headers = array_map(fn($col) => $col['label'], $columns);

        $sheet->fromArray([$headers], null, 'A1');
        $rowIndex = 2;

        $query = $this->query($filters);

        $query->chunk(2000, function ($rows) use ($sheet, &$rowIndex, $columns) {

            $data = [];

            foreach ($rows as $row) {
                $data[] = $this->mapRow($row, $columns);
            }

            $sheet->fromArray($data, null, 'A' . $rowIndex);

            $rowIndex += count($data);
        });

        $sheet->getStyle('Q:Q') // adjust column index for expected_duration
            ->getNumberFormat()
            ->setFormatCode('[h]:mm');

        $sheet->getStyle('R:R') // real_duration
            ->getNumberFormat()
            ->setFormatCode('[h]:mm');

        foreach (range('A', 'T') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName);
    }

    private function query(array $filters)
    {
        // dd(data_get($filters, 'department.values'));
        $departmentIds = data_get($filters, 'department.values');

        return WorkActivityReport::query()
            ->select(array_column($this->columns(), 'key'))
            ->when(data_get($filters, 'activity_date.activity_date_from'), fn($q, $v) => $q->whereDate('activity_date', '>=', $v))
            ->when(data_get($filters, 'activity_date.activity_date_to'), fn($q, $v) => $q->whereDate('activity_date', '<=', $v))
            ->when(!empty($departmentIds), function($q) use ($departmentIds) {
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

    private function formatValue($value, ?string $format)
    {
        return match ($format) {

            // Excel date/time
            // 'date' => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null,

            // duration → Excel time ([h]:mm)
            // 'duration' => str_replace('.', ',', $value < 0
            'duration' => $value < 0
                ? 0
                : ($value / 86400),
            
            // 'duration' => $value < 0
            //     ? '00:00'
            //     : gmdate('H:i', $value),

            // boolean mapping
            // 'bool' => match ($value) {
            //     0 => 'Nie',
            //     1 => 'Áno',
            //     default => 'Nevyhodnotené'
            // },

            default => $value,
        };
    }

    private function mapRow($row, array $columns): array
    {
        // return $row->toArray();
        $mapped = [];

        foreach ($columns as $col) {
            $value = data_get($row, $col['key']);

            $mapped[] = $this->formatValue($value, $col['type'] ?? null);
        }

        return $mapped;
    }

    public function export(array $filters, string $fileName)
    {
        $writer = new Writer();
        $writer->openToBrowser($fileName);

        $columns = $this->columns();

        // HEADER
        $writer->addRow(Row::fromValues(
            array_map(fn($c) => $c['label'], $columns)
        ));

        // DATA
        $query = $this->query($filters);
        // dd($query->count());
            $query->chunk(2000, function ($rows) use ($writer, $columns) {

                foreach ($rows as $row) {

                    $writer->addRow(
                        Row::fromValues(
                            array_map(function ($col) use ($row) {
                                $value = data_get($row, $col['key']);
                                return $this->formatValue($value, $col['type'] ?? null);
                            }, $columns)
                        )
                    );
                }
            });
    

        $writer->close();

        // exit;
    }
}
