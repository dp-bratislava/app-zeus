<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class TicketData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        #[DataCollectionOf(ActivityData::class)]
        public DataCollection $activities,
        public float $expenses = 0.75,
        #[DataCollectionOf(MaterialData::class)]
        public ?DataCollection $materials = null,
        public ?TicketData $parent = null,
        public ?DepartmentData $department = null,
        public ?EmployeeContractData $author = null,
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
