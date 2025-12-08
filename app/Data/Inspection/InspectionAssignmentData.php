<?php

namespace App\Data\Inspection;

use Spatie\LaravelData\Data;

class InspectionAssignmentData extends Data
{
    public function __construct(
        public ?int $id,
        public ?InspectionData $inspection,
        public int $subject_id,
        public string $subject_type,
        // public int $author_id,
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
