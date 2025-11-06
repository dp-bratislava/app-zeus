<?php

namespace App\Services;

use App\Models\DailyExpedition;
use Illuminate\Support\Carbon;

class DailyExpeditionRepository
{
    public function __construct(
        // protected Activity $workIntervalModel,
        protected DailyExpedition $dailyExpeditionModel,
    ) {}

    public function bulkCreate(array $data)
    {
        $now = Carbon::now();
        $deData = [];
        foreach ($data['vehicles'] as $key => $vehicleData) {
            $deData[] = [
                'date' => $data['date'],
                'vehicle_id' => $vehicleData['vehicle_id'],
                'state' => $vehicleData['state'],
                'service' => $vehicleData['service'],
                'note' => $vehicleData['note'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->dailyExpeditionModel->insert($deData);
    }
}
