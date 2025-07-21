<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user_list = Permission::create(['name' => 'user-list']);
        $user_view = Permission::create(['name' => 'user-view']);
        $user_create = Permission::create(['name' => 'user-create']);
        $user_edit = Permission::create(['name' => 'user-edit']);
        $user_delete = Permission::create(['name' => 'user-delete']);
        $user_export = Permission::create(['name' => 'user-export']);


        $admin_role = Role::create(['name' => 'admin']);
        $admin_role->givePermissionTo([
            $user_list,
            $user_view,
            $user_create,
            $user_edit,
            $user_delete,
            $user_export
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password')
        ]);

        $admin->assignRole($admin_role);
        $admin->givePermissionTo([
            $user_list,
            $user_view,
            $user_create,
            $user_edit,
            $user_delete,
            $user_export
        ]);

        $user = User::create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password')
        ]);

        $user->assignRole($admin_role);
        $user->givePermissionTo([
            $user_list,
            $user_view,
            $user_edit
        ]);
    }
}
