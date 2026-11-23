<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            Role::ADMINISTRATOR,
            Role::HOTEL_MANAGER,
            Role::RECEPTIONIST,
            Role::CUSTOMER,
            Role::SUPPLIER,
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }
}
