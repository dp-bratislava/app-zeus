<?php

namespace App\Data\Ticket;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class TicketAssignmentData extends Data
{
    public function __construct(
        public ?int $id,
        public ?TicketData $ticket,
        public int $subject_id,
        public string $subject_type,
        public int $source_id,
        public string $source_type,
        public int $author_id,
        public ?int $assigned_to_id,
        public ?string $assigned_to_type,

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
