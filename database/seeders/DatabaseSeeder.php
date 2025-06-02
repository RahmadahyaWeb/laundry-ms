<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'thelastpc24@gmail.com'],
            [
                'name' => 'Rahmadahya',
                'password' => Hash::make('password')
            ]
        );

        $permissions = [
            'view dashboard',

            // USER
            'view user',
            'edit user',
            'delete user',

            // ROLE
            'view role',
            'edit role',
            'delete role',

            // SERVICE CATEGORY
            'view service-category',
            'edit service-category',
            'delete service-category',

            // SERVICE
            'view service',
            'edit service',
            'delete service',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role = Role::firstOrCreate(['name' => 'admin']);

        $role->syncPermissions($permissions);

        if (!$admin->hasRole($role->name)) {
            $admin->assignRole($role);
        }
    }
}
