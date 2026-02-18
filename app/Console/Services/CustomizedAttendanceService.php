<?php

namespace App\Console\Services;

use Carbon\Carbon;
use Dpb\DatahubSync\Models\Attendance\Shift;
use Dpb\DatahubSync\Models\EmployeeContract;
use Dpb\WorkTimeFund\Models\WorkTime;
use Dpb\WorkTimeFund\Models\ActivityRecord;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomizedAttendanceService
{
    private int $departmentId;
    private string $host;
    private string $token;
    private string $monthType;
    private ?string $customDate = null;

    private const ENDPOINT = '/api/v2/attendance/attendance';
    private const PER_PAGE = 2500;
    private const MONTH_TYPES = ['current', 'next'];

    public function __construct()
    {
        $this->host = config('datahub-sync.server.host');
        $this->token = config('datahub-sync.server.token');
    }

    public function prefillEmployeeAttendance(int $departmentId, string $monthType = 'current', $customDate = null): array
    {
        $this->departmentId = $departmentId;

        $monthType = strtolower($monthType);

        if (!in_array($monthType, self::MONTH_TYPES)) {
            return $this->result(false, 'Bol vybraný neplatný typ mesiaca. Skontrolujte prosím nastavenie a skúste to znova.');
        }

        $this->monthType = $monthType;
        if($customDate){
            $this->customDate = $customDate;
        }

        try {
            return $this->updateAttendance();
        } catch (Exception $e) {
            Log::error("CustomizedAttendanceService failed for department {$departmentId}, month '{$monthType}': ".$e->getMessage(), [
                'exception' => $e
            ]);

            return $this->result(false, 'Nepodarilo sa aktualizovať dochádzku. Skúste to prosím neskôr alebo kontaktujte administrátora.');
        }
    }

    private function updateAttendance(): array
    {
        $activityRecords = $this->getActivityRecords();
        $contracts = $this->getContracts();
        $shifts = $this->getShifts();

        if ($contracts->isEmpty()) {
            return $this->result(false, 'Pre aktuálne oddelenie neboli nájdené žiadne zmluvy.');
        }
        
        if ($shifts->isEmpty()) {
            return $this->result(false, 'V systéme nie sú nastavené žiadne pracovné zmeny.');
        }

        $data = $this->fetchAttendanceData($contracts);
        $worktimes = $this->prepareWorktimes($data, $contracts, $shifts, $activityRecords);

        if (!empty($worktimes)) {
            WorkTime::insertOrIgnore($worktimes);
            return $this->result(true, 'Dochádzka bola úspešne spracovaná. Pridaných záznamov: '.count($worktimes).'.');
        }

        Log::info("No worktimes to insert for department {$this->departmentId}");
        return $this->result(true, 'Neexistujú žiadne nové záznamy dochádzky na spracovanie.');
    }

    private function fetchAttendanceData($contracts): array
    {
        $response = Http::timeout(10)
            ->retry(3, 100)
            ->withToken($this->token)
            ->get($this->getUrl(), $this->getParams($contracts));

        if ($response->failed()) {
            throw new Exception('HTTP request failed: '.$response->body());
        }

        $object = $response->object();

        if (!isset($object->data) || !is_array($object->data)) {
            throw new Exception('Invalid response format: '.json_encode($object));
        }

        return $object->data;
    }

    private function prepareWorktimes(array $data, $contracts, $shifts, $activityRecords): array
    {
        $worktimesToInsert = [];

        foreach ($data as $item) {
            $contract = $contracts->get($item->attributes->datahub_contract_id);
            $shift = $shifts->get($item->attributes->datahub_attendance_shift_id);

            if (!$contract || !$shift) {
                Log::warning('Skipping invalid item', ['item' => $item]);
                continue;
            }

            $key = $contract->pid . '|' . $item->attributes->date;

            if ($activityRecords->has($key)) {
                continue;
            }

            $worktimesToInsert[] = [
                'date' => Carbon::parse($item->attributes->date)->format('Y-m-d'),
                'shift' => $item->attributes->datahub_attendance_shift_id,
                'first_name' => $contract->employee->first_name,
                'last_name' => $contract->employee->last_name,
                'personal_id' => $contract->pid,
                'shift_start' => $shift->time_from->format('H:i:s'),
                'shift_duration' => $shift->duration * 60,
                'department' => $contract->datahub_department_id,
            ];
        }

        return $worktimesToInsert;
    }

    private function getUrl(): string
    {
        return $this->host . self::ENDPOINT;
    }

    private function getParams($contracts): array
    {
        return [
            'page[size]' => self::PER_PAGE,
            'filter' => [
                'date' => $this->getStartDate(),
                'datahub_contract_id' => $contracts->pluck('id')->toArray(),
                'contract_circuit_code' => implode(',', config('dpb-em.allowed_circuit_codes')),
            ],
        ];
    }

    private function getStartDate(): string
    {
        $now = Carbon::now()->startOfMonth();

        if($this->customDate) {
            return Carbon::parse($this->customDate)->startOfMonth()->format('Y-m-d');
        }

        return match ($this->monthType) {
            'current' => $now->format('Y-m-d'),
            'next' => $now->addMonth()->format('Y-m-d'),
            default => throw new \InvalidArgumentException(message: "Invalid monthType: {$this->monthType}"),
        };
    }

    private function getContracts()
    {
        return EmployeeContract::query()
            ->with('employee:id,first_name,last_name')
            ->select('id', 'pid', 'datahub_employee_id', 'datahub_department_id')
            ->where('datahub_department_id', $this->departmentId)
            ->where('is_active', 1)
            ->get()
            ->keyBy('id');
    }

    private function getShifts()
    {
        return Shift::query()
            ->select('id', 'time_from', 'duration')
            ->get()
            ->keyBy('id');
    }

    private function getActivityRecords(): Collection
    {
        // TODO: Select only necessary columns
        return ActivityRecord::query()
            ->where('department_id', $this->departmentId)
            ->get()
            ->groupBy(fn ($wt) => $wt->personal_id . '|' . $wt->date);
    }

    private function result(bool $success, string $message): array
    {
        return [
            'success' => $success,
            'message' => $message,
        ];
    }
}
