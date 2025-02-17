<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // User::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => Hash::make('adminpassword'),
        //     'role' => 'admin',
        // ]);

        User::create([
            'name' => 'Jerel',
            'email' => 'jerel.paligumba10@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'cluster',
        ]);

        User::create([
            'name' => 'Brice',
            'email' => 'bricebuenaventura@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'barangay',
        ]);
    }
}
