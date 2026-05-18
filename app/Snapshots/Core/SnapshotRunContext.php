<?php

namespace App\Snapshots\Core;

class SnapshotRunContext
{
    public function __construct(
        public ?string $from = null,
        public ?string $to = null,
        public bool $all = false,
        public bool $force = false,
    ) {}
}