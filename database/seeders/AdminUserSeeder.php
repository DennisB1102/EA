<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@ea.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
            ]
        );
        $admin->assignRole('admin');
    }
}
