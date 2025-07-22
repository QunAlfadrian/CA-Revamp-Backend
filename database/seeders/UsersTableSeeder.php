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
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin')
        ]);
        $user = User::where('email', 'admin@example.com')->first();
        $user->actingAs(Role::admin());

        User::factory()->count(20)->create();
        $users = User::all();
        foreach ($users as $user) {
            $user->actingAs(Role::donor());
        }

        $users = User::latest('created_at')->limit(5)->get();
        foreach ($users as $user) {
            $user->actingAs(Role::organizer());
        }

        $user = User::create([
            'name' => 'kyuib',
            'email' => 'kyuib@example.com',
            'password' => Hash::make('Bulat1234'),
        ]);
        $user = User::where('email', 'kyuib@example.com')->first();
        $user->actingAs(Role::donor());

        $user = User::create([
            'name' => 'emi',
            'email' => 'emi@example.com',
            'password' => Hash::make('Bulat1234'),
        ]);
        $user = User::where('email', 'emi@example.com')->first();
        $user->actingAs(Role::donor());
        $user->actingAs(Role::organizer());
    }
}
