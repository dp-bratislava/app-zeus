<?php

namespace App\Data\Inspection;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class InspectionData extends Data
{
    public function __construct(
        public ?int $id,
        public Carbon $date,
        public int $template_id,
        public string $state,
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
