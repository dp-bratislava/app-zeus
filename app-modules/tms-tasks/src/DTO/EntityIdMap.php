<?php

namespace Dpb\Modules\Tasks\DTO;

use Illuminate\Support\Collection;
use RuntimeException;

final readonly class EntityIdMap
{
    public function __construct(
        private readonly Collection $idsByUuid,
    ) {}

    public function id(string $uuid): int
    {
        $id = $this->idsByUuid->get($uuid);

        if ($id === null) {
            throw new RuntimeException("Entity not found for UUID [{$uuid}].");
        }

        return $id;
    }

    public function has(string $uuid): bool
    {
        return $this->idsByUuid->has($uuid);
    }

    public function all(): array
    {
        return $this->idsByUuid->all();
    }

    public function ids(): array
    {
        return $this->idsByUuid->values()->all();
    }
}
