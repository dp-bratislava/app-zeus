<?php

namespace App\Snapshots\Core;

class TempTableManager
{
    public function __construct(
        protected \Illuminate\Database\ConnectionInterface $db
    ) {}

    public function create(string $table): void
    {
        $this->db->statement("
            CREATE TEMPORARY TABLE {$table} (
                id BIGINT PRIMARY KEY
            )
        ");
    }

    public function insertIds(string $table, array $ids): void
    {
        $this->db->table($table)->insert(
            array_map(fn($id) => ['id' => $id], $ids)
        );
    }

    public function clear(string $table): void
    {
        $this->db->statement("DELETE FROM {$table}");
    }

    public function drop(string $table): void
    {
        $this->db->statement("DROP TEMPORARY TABLE IF EXISTS {$table}");
    }
}
