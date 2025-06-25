<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        for ($i = 0; $i < 5; $i++) {
            User::factory()->create([
                'name' => fake()->userName(),
                'email' => fake()->safeEmail(),
            ]);
        }

        $role = Role::where('name', 'donor')->first();
        $users = User::all();
        foreach ($users as $user) {
            $user->actingAs($role);
        }
    }
}
