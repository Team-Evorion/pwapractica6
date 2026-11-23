<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Supply;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            RoomSeeder::class,
        ]);

        $configuredPassword = env('INITIAL_ADMIN_PASSWORD');
        if (! is_string($configuredPassword) || $configuredPassword === '') {
            return;
        }

        $adminRole = Role::where('name', Role::ADMINISTRATOR)->firstOrFail();
        $admin = User::firstOrCreate(
            ['email' => 'admin@hotel.test'],
            [
                'name' => 'Administrador Inicial',
                'password' => $configuredPassword,
                'role_id' => $adminRole->id,
                'is_active' => true,
            ],
        );

        $needs = [
            ['Juego de sábanas algodón 300 hilos', 50, 'Reposición para habitaciones estándar y suites.'],
            ['Kit de amenidades de baño', 200, 'Entrega mensual para recepción y limpieza.'],
        ];

        foreach ($needs as [$name, $quantity, $notes]) {
            Supply::firstOrCreate(
                ['name' => $name, 'item_type' => 'need'],
                [
                    'quantity' => $quantity,
                    'supplier_id' => $admin->id,
                    'status' => 'open',
                    'notes' => $notes,
                ],
            );
        }
    }
}
