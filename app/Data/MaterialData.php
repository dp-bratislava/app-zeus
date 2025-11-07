<?php

namespace App\Data;

use DateTime;
use Spatie\LaravelData\Data;

class MaterialData extends Data
{
    public function __construct(
        public int $id,
        public DateTime $date,
        public ?string $code,
        public ?string $title,
        public ?string $description,
        public ?float $price,
        public ?float $vat,
    ) {}
}
