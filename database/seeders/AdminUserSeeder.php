<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        if(!$adminRole) {
            return;
        }

        $admin = User::firstOrNew(['email' => 'admin@example.com']);
        $admin->role_id = $adminRole->id;
        $admin->name = 'Admin';
        $admin->is_active = true;
        $admin->email_verified_at = now();

        if (! $admin->exists) {
            $admin->password = 'admin12345';
        }

        $admin->save();
    }
}
