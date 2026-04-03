<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(['name' => 'admin'], [
            'name' => 'admin',
            'description' => 'Administrator with full access.',
        ]);
        Role::updateOrCreate(['name' => 'moderator'], [
           'name' => 'moderator',
           'description' => 'Moderator who manages reports and comments.',
        ]);
        Role::updateOrCreate(['name' => 'user'], [
            'name' => 'user',
            'description' => 'Regular user.',
        ]);
    }
}
