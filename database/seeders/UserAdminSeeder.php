<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Sair Sánchez Valderrama',
            'email' => 'sairjsanchez@gmail.com',
            'password' => bcrypt("123456789"),
        ]);

        $user->assignRole('admin');
    }
}
