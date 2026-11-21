<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Supply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $adminRole = Role::where('name', Role::ADMINISTRATOR)->first();
        $supplierRole = Role::where('name', Role::SUPPLIER)->first();

        $admin = null;
        if ($adminRole) {
            $admin = User::firstOrCreate(
                ['email' => 'admin@hotel.test'],
                [
                    'name' => 'Administrador Inicial',
                    'password' => Hash::make('Admin1234!'),
                    'role_id' => $adminRole->id,
                    'is_active' => true,
                ]
            );
        }

        if ($admin && $supplierRole) {
            $needs = [
                [
                    'name' => 'Juego de sabanas algodon 300 hilos',
                    'quantity' => 50,
                    'notes' => 'Reposicion para habitaciones estandar y suites.',
                ],
                [
                    'name' => 'Kit de amenidades de bano (jabon, shampoo, locion)',
                    'quantity' => 200,
                    'notes' => 'Entrega mensual para recepcion y limpieza.',
                ],
            ];

            foreach ($needs as $needData) {
                Supply::firstOrCreate(
                    [
                        'name' => $needData['name'],
                        'item_type' => 'need',
                    ],
                    [
                        'quantity' => $needData['quantity'],
                        'supplier_id' => $admin->id,
                        'status' => 'open',
                        'notes' => $needData['notes'],
                    ]
                );
            }
        }
    }
}