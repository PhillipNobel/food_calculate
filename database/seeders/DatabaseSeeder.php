<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Criar papéis
        $admin = Role::create(['name' => 'administrator']);
        $client = Role::create(['name' => 'client']);
        $guest = Role::create(['name' => 'guest']);
        // User::factory(10)->create();

        $admin->givePermissionTo(['view filament', 'create users', 'manage users', 'other admin permissions']);
        $client->givePermissionTo(['view filament', 'create users']);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
