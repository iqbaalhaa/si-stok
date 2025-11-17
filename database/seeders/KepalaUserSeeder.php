<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class KepalaUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate([
            'name' => 'kepala',
            'guard_name' => 'web',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'kepala@kepala.com'],
            [
                'name' => 'Kepala',
                'password' => 'password',
                'role' => 'kepala',
            ]
        );

        $user->assignRole($role);
    }
}