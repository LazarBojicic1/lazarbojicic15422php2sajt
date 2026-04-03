<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class ModeratorUserSeeder extends Seeder
{
    public function run(): void
    {
        $moderatorRole = Role::where('name', 'moderator')->first();
        if (!$moderatorRole) {
            return;
        }

        $moderator = User::firstOrNew(['email' => 'moderator@example.com']);
        $moderator->role_id = $moderatorRole->id;
        $moderator->name = 'Moderator';
        $moderator->is_active = true;
        $moderator->email_verified_at = now();

        if (! $moderator->exists) {
            $moderator->password = 'moderator12345';
        }

        $moderator->save();
    }
}
