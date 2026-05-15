<?php

namespace App\Reports\Exports;

use App\Models\Reports\Export;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

abstract class BaseReportExporter
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = Config::get('reports.exporter_url');
    }

    /**
     * Get the columns configuration for export
     */
    abstract protected function columns(): array;

    /**
     * Build the base query for export
     */
    abstract protected function query(array $filters);

    /**
     * Handle the export payload
     */
    public function handlePayload($filters)
    {
        $query = $this->query($filters);

        $response = Http::timeout(300)->post("$this->baseUrl/export/generate", [
            'sql'     => $query->toSql(),
            'params'  => $query->getBindings(),
            'columns' => $this->columns()
        ]);

        if (!$response->successful()) {
            throw new \Exception("Exporter Service Error (" . $response->status() . "): " . $response->body());
        }

        return $response->json();
    }

    /**
     * Store the exported file locally
     */
    public function storeFile($fileUrl, $fileName)
    {
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

    /**
     * Get the disk name for storing exports
     */
    protected function getStorageDisk(): string
    {
        return 'report-exports';
    }

    /**
     * Get the exporter name/type
     */
    abstract public function getExporterName(): string;

    /**
     * Run the export process
     */
    public function run(array $filters, $userId, string $fileName): Export
    {
        $responseData = $this->handlePayload($filters);

        if (!isset($responseData['url'])) {
            throw new \Exception("Export failed: " . json_encode($responseData));
        }

        $localFileName = $this->storeFile($responseData['url'], $fileName);

        return Export::create([
            'completed_at' => now(),
            'exporter' => $this->getExporterName(),
            'file_disk' => $this->getStorageDisk(),
            'file_name' => $localFileName,
            'processed_rows' => 0,
            'successful_rows' => 0,
            'total_rows' => 0,
            'user_id' => $userId,
        ]);
    }
}
