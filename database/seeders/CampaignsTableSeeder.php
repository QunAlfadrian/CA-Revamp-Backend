<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CampaignsTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $users = User::whereHas('roleRelation', function ($q) {
            $q->where('name', 'organizer');
        })->get();

        foreach ($users as $user) {
            $campaign = Campaign::factory()->count(2)->create([
                'organizer_id' => $user->id,
            ]);
        }
    }
}
