<?php

namespace App\Data\Ticket;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class TicketItemData extends Data
{
    public function __construct(
        public int $id,
        public string $date,
        public int $ticketId,
        public string $title,
        public string $description,
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
