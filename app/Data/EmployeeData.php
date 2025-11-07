<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class EmployeeData extends Data
{
    public function __construct(
        public int $id,
        public ?string $lastName
    ) {}
}
