<?php

namespace App\Snapshots\Core;

use App\Snapshots\Core\Contracts\SnapshotContract;
use Error;

class SnapshotRegistry
{
    protected array $snapshots = [];

    public function register(string $key, string $snapshotClass): void
    {
        $this->snapshots[$key] = $snapshotClass;
    }

    public function resolve(string $key): SnapshotContract
    {
        try {
            if (!isset($this->snapshots[$key])) {
                throw new \InvalidArgumentException("Snapshot [$key] not registered");
            }

            $class = $this->snapshots[$key];

            return app($class);
        } catch (\Throwable $e) {
            logger()->error('Snapshot resolve failed', [
                'key' => $key,
                'exception' => $e,
            ]);

            throw $e; // IMPORTANT: don’t swallow it
        }
    }
}
