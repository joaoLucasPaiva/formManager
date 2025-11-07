<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verifica se o usuário admin já existe
        $adminExists = User::where('email', 'admin@admin.com')->exists();

        if ($adminExists) {
            $this->command->info('Usuário admin já existe!');
            return;
        }

        // Cria o usuário admin
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        // Atribui a role de owner
        $user->assignRole('owner');

        $this->command->info('✅ Usuário admin criado com sucesso!');
        $this->command->info('📧 Email: admin@admin.com');
        $this->command->info('🔑 Senha: password');
    }
}
