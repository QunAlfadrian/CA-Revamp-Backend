<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        User::factory()->count(10)->create();
        $role = Role::where('name', 'donor')->first();
        $users = User::all();
        foreach ($users as $user) {
            $user->actingAs($role);
        }

        User::factory()->count(5)->create();
        $role = Role::where('name', 'organizer')->first();
        $users = User::latest()->limit(5)->get();
        foreach ($users as $user) {
            $user->actingAs($role);
        }

        $user = User::create([
            'name' => 'kyuib',
            'email' => 'kyuib@example.com',
            'password' => Hash::make('Bulat1234'),
        ]);
        $user = User::where('email', 'kyuib@example.com')->first();
        $user->actingAs($role);
    }
}
