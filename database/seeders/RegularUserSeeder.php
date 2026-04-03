<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RegularUserSeeder extends Seeder
{
    public function run(): void
    {
        $userRole = Role::where('name', 'user')->first();

        if (! $userRole) {
            return;
        }

        $accounts = [
            [
                'email' => 'user@example.com',
                'name' => 'User',
                'password' => 'user12345',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'email' => 'viewer@example.com',
                'name' => 'Viewer',
                'password' => 'viewer12345',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'email' => 'pendinguser@example.com',
                'name' => 'PendingUser',
                'password' => 'pending12345',
                'is_active' => true,
                'email_verified_at' => null,
            ],
        ];

        foreach ($accounts as $account) {
            $user = User::firstOrNew(['email' => $account['email']]);
            $user->role_id = $userRole->id;
            $user->name = $account['name'];
            $user->is_active = $account['is_active'];
            $user->email_verified_at = $account['email_verified_at'];

            if (! $user->exists) {
                $user->password = $account['password'];
            }

            $user->save();
        }
    }
}
