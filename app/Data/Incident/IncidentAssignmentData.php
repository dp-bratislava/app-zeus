<?php

namespace App\Data\Incident;

use Spatie\LaravelData\Data;

class IncidentAssignmentData extends Data
{
    public function __construct(
        public ?int $id,
        public ?IncidentData $incident,
        public int $subject_id,
        public string $subject_type,
        public int $author_id,
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
