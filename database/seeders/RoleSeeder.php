<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Role::insert($this->mapRolesArray([
            'user',
            'admin',
            'super-admin',
            'veduci-pracovnik',
        ]));
    }

    private function mapRolesArray(array $roles): array
    {
        return array_map(fn ($item) => [
            'name' => $item,
            'guard_name' => 'web',
        ], $roles);
    }
}
