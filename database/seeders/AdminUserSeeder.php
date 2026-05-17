<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'is_active' => true,
                'preferred_language' => 'tr',
                'email_verified_at' => now(),
            ]
        )->syncRoles(['super_admin']);

        User::query()
            ->where('is_admin', true)
            ->get()
            ->each
            ->syncRoles(['super_admin']);
    }
}
