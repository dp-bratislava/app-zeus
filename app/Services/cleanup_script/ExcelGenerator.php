<?php

namespace App\Services\cleanup_script;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\Storage;
use Exception;

class ExcelGenerator
{
    private string $disk = 'local';
    private string $path = 'excel-exports';
    private Spreadsheet $spreadsheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
    }

    /**
     * Generate Excel file from array of data
     *
     * @param array $data Array of records with ids and info
     * @param string $title Sheet title
     * @param array $columns Column headers (optional, auto-generated if not provided)
     * @return string Path to saved file
     * @throws Exception
     */
    public function generate(array $data, string $title = 'Data Export', array $columns = []): string
    {
        if (empty($data)) {
            throw new Exception('Data array cannot be empty');
        }

        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle($title);

        // Get columns from data if not provided
        if (empty($columns)) {
            $columns = array_keys($data[0]);
        }

        // Write headers
        $this->writeHeaders($sheet, $columns);

        // Write data rows
        $this->writeData($sheet, $data, $columns);

        // Auto-fit columns
        $this->autoFitColumns($sheet, $columns);

        // Save and return path
        return $this->save();
    }

    /**
     * Write headers to the first row
     */
    private function writeHeaders($sheet, array $columns): void
    {
        $col = 1;
        foreach ($columns as $header) {
            $cell = $sheet->getCellByColumnAndRow($col, 1);
            $cell->setValue(ucfirst(str_replace('_', ' ', $header)));

            // Style headers
            $cell->getFont()->setBold(true);
            $cell->getFont()->setColor(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
            $cell->getFill()->setFillType(Fill::FILL_SOLID);
            $cell->getFill()->getStartColor()->setARGB('FF4472C4');
            $cell->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $cell->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $col++;
        }
    }

    /**
     * Write data rows
     */
    private function writeData($sheet, array $data, array $columns): void
    {
        $row = 2;
        foreach ($data as $record) {
            $col = 1;
            foreach ($columns as $column) {
                $value = $record[$column] ?? null;
                $sheet->getCellByColumnAndRow($col, $row)->setValue($value);
                $col++;
            }
            $row++;
        }
    }

    /**
     * Auto-fit column widths based on content
     */
    private function autoFitColumns($sheet, array $columns): void
    {
        $col = 1;
        foreach ($columns as $column) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
            $col++;
        }
    }

    /**
     * Set custom disk for storage
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * Set custom path within the disk
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Save Excel file to storage
     */
    private function save(): string
    {
        try {
            // Ensure directory exists
            Storage::disk($this->disk)->makeDirectory($this->path, 0755, true);

            // Generate filename with timestamp
            $filename = 'export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
            $filepath = $this->path . '/' . $filename;

            // Write to temporary location
            $tempFile = storage_path('app/temp_' . $filename);
            $writer = new Xlsx($this->spreadsheet);
            $writer->save($tempFile);

            // Move to target location
            $content = file_get_contents($tempFile);
            Storage::disk($this->disk)->put($filepath, $content);

            // Clean up temp file
            @unlink($tempFile);

            return $filepath;
        } catch (Exception $e) {
            throw new Exception('Failed to save Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Get full URL to the saved file
     */
    public function getUrl(string $filepath): string
    {
        return Storage::disk($this->disk)->url($filepath);
    }

    /**
     * Download the generated file
     */
    public function download(string $filename = 'export.xlsx')
    {
        $writer = new Xlsx($this->spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        $writer->save('php://output');
    }
}
