<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'mdmithurahman40@gmail.com'],
            [
                'name' => 'Md. Mithu Rahman',
                'password' => \Illuminate\Support\Facades\Hash::make('Mithu@1713183575'),
                'role' => \App\Models\User::ROLE_SUPER_ADMIN,
            ]
        );
    }
}
