<?php

namespace App\Filament\Exports\Reports;

use App\Models\Reports\Export;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Vtiful\Kernel\Excel;
use Vtiful\Kernel\Format;

class WorkActivityReportExporter
{
    protected string $baseUrl = 'http://10.10.11.224:8111';

    public function start(): int
    {
        $columns = $this->columns();
        $headers = array_map(fn($col) => $col['label'], $columns);

        $response = Http::post("$this->baseUrl/export/start", [
            'header' => $headers,
        ]);

        if (!$response->successful()) {
            throw new \Exception("Export start failed: " . $response->body());
        }

        $id = $response->json('export_id');

        if (!$id) {
            throw new \Exception("Missing export_id in response: " . $response->body());
        }

        return (int) $id;
    }

    public function finish($exportId): string
    {
        $response = Http::post("$this->baseUrl/export/$exportId/finish");

        if (!$response->successful()) {
            throw new \Exception("Export finish failed: " . $response->body());
        }
        $url = $response->json('url');

        if (!$url) {
            throw new \Exception("Missing url in response: " . $response->body());
        }

        return (string) $url;
    }

    public function handlePayload($filters, $exportId)
    {
        $query = $this->query($filters);

        $query->chunkById(2000, function ($rows) use ($exportId) {
            $batch = [];
            foreach ($rows as $row) {
                $batch[] = [
                    (string)$row->department_code,
                    (string)$row->task_created_at,
                    (string)$row->task_date,
                    (string)$row->task_group_title,
                    (string)$row->task_assigned_to_label,
                    (string)$row->task_author_lastname,
                    (string)$row->task_item_group_title,
                    (string)$row->task_item_assigned_to_label,
                    (string)$row->task_item_author_lastname,
                    (string)$row->wtf_task_created_at,
                    (string)$row->activity_date,
                    (string)$row->personal_id,
                    (string)$row->last_name,
                    (string)$row->first_name,
                    (string)$row->activity_title,
                    // Duration Calculations (using float for Excel date/time)
                    $row->activity_expected_duration < 0
                        ? ($row->activity_real_duration / 86400)
                        : ($row->activity_expected_duration / 86400),
                    $row->activity_real_duration < 0
                        ? 0
                        : ($row->activity_real_duration / 86400),
                    match ($row->activity_is_fulfilled) {
                        0 => 'Nie',
                        1 => 'Áno',
                        default => 'Nevyhodnotené',
                    },
                ];
            }

            Http::post("$this->baseUrl/export/$exportId/chunk", [
                'rows' => $batch
            ]);
        });
    }


    public function storeFile($fileUrl)
    {
        $file = Http::get($fileUrl);
        Storage::disk('report-exports')->put(
            'wa_' . now()->format('Ymd_His') . '.xlsx',
            $file->body()
        );
    }

    public function run(array $filters)
    {
        $exportId = $this->start();
        $this->handlePayload($filters, $exportId);
        $fileUrl = $this->finish($exportId);
        $this->storeFile($fileUrl);
    }

    public function getPayload(array $filters)
    {
        $columns = $this->columns();
        $headers = array_map(fn($col) => $col['label'], $columns);

        $query = $this->query($filters);
        $data = [];

        $query->chunkById(2000, function ($rows) use (&$data) {
            foreach ($rows as $row) {
                $data[] = [
                    (string)$row->department_code,
                    (string)$row->task_created_at,
                    (string)$row->task_date,
                    (string)$row->task_group_title,
                    (string)$row->task_assigned_to_label,
                    (string)$row->task_author_lastname,
                    (string)$row->task_item_group_title,
                    (string)$row->task_item_assigned_to_label,
                    (string)$row->task_item_author_lastname,
                    (string)$row->wtf_task_created_at,
                    (string)$row->activity_date,
                    (string)$row->personal_id,
                    (string)$row->last_name,
                    (string)$row->first_name,
                    (string)$row->activity_title,
                    // Duration Calculations (using float for Excel date/time)
                    $row->activity_expected_duration < 0
                        ? ($row->activity_real_duration / 86400)
                        : ($row->activity_expected_duration / 86400),
                    $row->activity_real_duration < 0
                        ? 0
                        : ($row->activity_real_duration / 86400),
                    match ($row->activity_is_fulfilled) {
                        0 => 'Nie',
                        1 => 'Áno',
                        default => 'Nevyhodnotené',
                    },
                ];
            }
        });

        return [
            'header' => $headers,
            'data' => $data
        ];
    }

    public function generate(array $filters, string $fileName, $userId)
    {
        $directory = Storage::disk('report-exports')->path('');

        // 1. Initialize XLSWriter
        $config = ['path' => $directory];
        $excel  = new Excel($config);

        // 2. Setup File and Resource Handle for Formatting
        $fileObject = $excel->fileName($fileName, 'Sheet1');
        $handle     = $fileObject->getHandle();

        // 3. Define Formats (Must be done via Format class using the handle)
        $format    = new Format($handle);
        $boldStyle = $format->bold()->toResource();

        $timeFormat = new Format($handle);
        $durationStyle = $timeFormat->number('[h]:mm')->toResource();

        // 4. Build headers
        $columns = $this->columns();
        $headers = array_map(fn($col) => $col['label'], $columns);

        // Write Header with Bold Style
        $fileObject->header($headers);
        // Note: XLSWriter doesn't have a single "apply to row" style like PhpSpreadsheet easily, 
        // so we often apply it during the write or via specific range methods.

        // 5. Processing Query
        $query = $this->query($filters);
        $totalRows = $query->count();

        $query->chunkById(2000, function ($rows) use ($fileObject, $durationStyle) {
            foreach ($rows as $row) {
                $data = [
                    (string)$row->department_code,
                    (string)$row->task_created_at,
                    (string)$row->task_date,
                    (string)$row->task_group_title,
                    (string)$row->task_assigned_to_label,
                    (string)$row->task_author_lastname,
                    (string)$row->task_item_group_title,
                    (string)$row->task_item_assigned_to_label,
                    (string)$row->task_item_author_lastname,
                    (string)$row->wtf_task_created_at,
                    (string)$row->activity_date,
                    (string)$row->personal_id,
                    (string)$row->last_name,
                    (string)$row->first_name,
                    (string)$row->activity_title,
                    // Duration Calculations (using float for Excel date/time)
                    $row->activity_expected_duration < 0
                        ? ($row->activity_real_duration / 86400)
                        : ($row->activity_expected_duration / 86400),
                    $row->activity_real_duration < 0
                        ? 0
                        : ($row->activity_real_duration / 86400),
                    match ($row->activity_is_fulfilled) {
                        0 => 'Nie',
                        1 => 'Áno',
                        default => 'Nevyhodnotené',
                    },
                ];

                // Write the row
                $fileObject->data([$data]);
            }
        });

        // 6. Apply "Post-Processing" Styles
        // Freeze pane (Row 1)
        $fileObject->freezePanes(1, 0);

        // Auto Filter - Using 'R' because you have 18 columns
        $fileObject->autoFilter('A1:R' . ($totalRows + 1));

        // Format the Duration Columns (P and Q)
        // setColumn(string $range, float $width, resource $format)
        $fileObject->setColumn('P:Q', 15, $durationStyle);

        // 7. Save and Close
        $fullPath = $fileObject->output();

        return Export::create([
            'completed_at' => now(),
            'exporter' => 'XLSWriter',
            'file_disk' => 'report-exports',
            'file_name' => pathinfo($fileName, PATHINFO_FILENAME),
            'processed_rows' => $totalRows,
            'successful_rows' => $totalRows,
            'total_rows' => $totalRows,
            'user_id' => $userId,
        ]);
    }

    private function query(array $filters)
    {
        $departmentCodes = data_get($filters, 'department.values');

        return DB::table('mvw_work_activity_report')
            ->select(['id', ...array_column($this->columns(), 'key')])
            ->when(data_get($filters, 'activity_date.activity_date_from'), fn($q, $v) => $q->whereDate('activity_date', '>=', $v))
            ->when(data_get($filters, 'activity_date.activity_date_to'), fn($q, $v) => $q->whereDate('activity_date', '<=', $v))
            ->when(!empty($departmentCodes), function ($q) use ($departmentCodes) {
                $q->whereIn('department_code', $departmentCodes);
            });
    }

    private function columns(): array
    {
        return [
            ['key' => 'department_code', 'label' => 'Stredisko'],
            ['key' => 'task_created_at', 'label' => 'Čas vytvorenia zákazky'],
            ['key' => 'task_date', 'label' => 'Dátum zákazky', 'type' => 'date'],
            ['key' => 'task_group_title', 'label' => 'Typ zákazky'],
            ['key' => 'task_assigned_to_label', 'label' => 'Prevádzka zákazky'],
            ['key' => 'task_author_lastname', 'label' => 'Zákzaku vytvoril'],
            ['key' => 'task_item_group_title', 'label' => 'Typ podzákazky'],
            ['key' => 'task_item_assigned_to_label', 'label' => 'Prevádzka podzákazky'],
            ['key' => 'task_item_author_lastname', 'label' => 'Podzákazku vytvoril'],
            ['key' => 'wtf_task_created_at', 'label' => 'Čas priradenia práce'],
            ['key' => 'activity_date', 'label' => 'Dátum výkonu práce', 'type' => 'date'],
            ['key' => 'personal_id', 'label' => 'Osobné číslo'],
            ['key' => 'last_name', 'label' => 'Priezvisko'],
            ['key' => 'first_name', 'label' => 'Meno'],
            ['key' => 'activity_title', 'label' => 'Norma'],
            ['key' => 'activity_expected_duration', 'label' => 'Norma trvanie', 'type' => 'duration'],
            ['key' => 'activity_real_duration', 'label' => 'Reálne trvanie', 'type' => 'duration'],
            ['key' => 'activity_is_fulfilled', 'label' => 'Splnené', 'type' => 'bool'],
        ];
    }
}
