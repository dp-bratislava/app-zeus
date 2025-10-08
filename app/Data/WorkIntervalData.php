<?php

namespace App\Data;

use DateTime;
use Illuminate\Support\Facades\Date;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class WorkIntervalData extends Data
{
    public function __construct(
        public int $id,
        public Date $date,
        public DateTime $timeFrom,
        public DateTime $timeTo,
        public int $duration,
    ) {}

    // public static function rules(): array
    // {
    //     return [
    //         'title' => ['required', 'string'],
    //         'description' => ['nullable', 'string'],
    //         'activities' => ['array'],
    //         'activities.*.activity_id' => ActivityAssignmentData::rules()['activity_id'],
    //         'activities.*.assigned_at' => ActivityAssignmentData::rules()['assigned_at'],
    //     ];
    // }
}
