<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions = [
            'create class',
            'edit presence',
            'delete presence',
            'view schedules',
            'view students',
            'create qrcode',
            'scan qrcode',
            'view status presence'
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // Assign permissions to roles
        foreach (Permission::all() as $permission) {
            $adminRole->givePermissionTo($permission);
        }

        foreach ([
            'view schedules',
            'view students',
            'create qrcode',
            'edit presence',
            'delete presence'
        ] as $permission) {
            $teacherRole->givePermissionTo($permission);
        }

        foreach ([
            'view schedules',
            'view status presence',
            'scan qrcode'
        ] as $permission) {
            $studentRole->givePermissionTo($permission);
        }

        // Create users and assign roles
        $user = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Administrator',
            'password' => bcrypt('12345678')
        ]);

        $teacher = User::firstOrCreate([
            'email' => 'teacher@example.com'
        ], [
            'name' => 'Teacher',
            'password' => bcrypt('12345678')
        ]);

        $student = User::firstOrCreate([
            'email' => 'student@example.com'
        ], [
            'name' => 'Student',
            'password' => bcrypt('12345678')
        ]);

        // Assign roles to users
        $user->assignRole($adminRole);
        $teacher->assignRole($teacherRole);
        $student->assignRole($studentRole);
    }
}
