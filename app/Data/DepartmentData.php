<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class DepartmentData extends Data
{
    public function __construct(
        public int $id,
        public ?string $code = null,
        public ?string $title = null,
    ) {}
}
