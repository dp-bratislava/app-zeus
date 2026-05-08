<?php

namespace App\Filament\Exports\Reports;

use App\Models\Reports\Export;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Vtiful\Kernel\Excel;
use Vtiful\Kernel\Format;
use Illuminate\Support\Facades\Log;

class WorkActivityReportExporter
{
    protected string $baseUrl = 'http://172.22.99.55:8111';

    public function start(): string
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

        return $id;
    }

    public function handlePayload($filters)
    {
        $query = $this->query($filters);
        
        // We get the headers from our columns definition
        $headers = array_map(fn($col) => $col['label'], $this->columns());

        $response = Http::timeout(300)->post("$this->baseUrl/export/generate", [
            'sql'    => $query->toSql(),
            'params' => $query->getBindings(),
            'header' => $headers
        ]);

        if (!$response->successful()) {
            throw new \Exception("Exporter Service Error: " . $response->body());
        }

        return $response->json();
    }

    public function storeFile($fileUrl, $fileName)
    {
    // If the service returns "files/name.xlsx", we need to make it 
    // "http://svc-excel-exporter-export-nginx-1:8111/files/name.xlsx"
    $fullUrl = str_starts_with($fileUrl, 'http') 
        ? $fileUrl 
        : rtrim($this->baseUrl, '/') . '/' . ltrim($fileUrl, '/');

    Log::info("Attempting to download export from: " . $fullUrl);

    $file = Http::timeout(120)->get($fullUrl);
    
    if (!$file->successful()) {
        throw new \Exception("Could not download file from exporter at: " . $fullUrl);
    }
    
    Storage::disk('report-exports')->put($fileName, $file->body());
    
    return $fileName;
}

public function run(array $filters,$userId, string $fileName): Export
    {
        // 1. Call the new all-in-one endpoint
        $responseData = $this->handlePayload($filters);

        if (!isset($responseData['url'])) {
            throw new \Exception("Export failed: " . json_encode($responseData));
        }

        $localFileName = $this->storeFile($responseData['url'], $fileName);
        
        // 3. Create the DB record using ALL 3 required arguments
        return Export::create([
            'completed_at' => now(),
            'exporter' => 'XLSWriter-Remote',
            'file_disk' => 'report-exports',
            'file_name' => $localFileName, // Use the actual filename saved by storeFile
            'processed_rows' => 123456789,
            'successful_rows' => 123456789,
            'total_rows' => 123456789,
            'user_id' => $userId,
            ]);
    }

    private function query(array $filters)
    {
        $departmentCodes = data_get($filters, 'department.values');

        return DB::table('mvw_work_activity_report_v2')
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
