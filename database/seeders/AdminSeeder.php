<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@kataloka.test'],
            [
                'name' => 'Admin Kataloka',
                'password' => Hash::make('AdminKataloka123!'),
                'role' => 'admin',
            ]
        );
    }
}
