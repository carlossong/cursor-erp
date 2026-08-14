<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $company = Company::factory()->create([
            'legal_name' => 'Empresa Demo Ltda',
            'trade_name' => 'Demo',
            'tax_id' => '12345678000190',
        ]);

        foreach ([
            'admin' => 'admin@local',
            'comercial' => 'comercial@local',
            'operacao' => 'operacao@local',
            'financeiro' => 'financeiro@local',
            'gestor' => 'gestor@local',
        ] as $role => $email) {
            $user = User::factory()->recycle($company)->create([
                'name' => str($role)->headline()->toString(),
                'email' => $email,
            ]);

            $user->assignRole($role);
        }
    }
}
