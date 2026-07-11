<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Roles available across the ERP, matching the proposal's
     * Multi User Management hierarchy.
     */
    public const ROLES = ['super-admin', 'admin', 'manager', 'salesman', 'accounts'];

    public function run(): void
    {
        collect(self::ROLES)->each(fn (string $role) => Role::findOrCreate($role));
    }
}
