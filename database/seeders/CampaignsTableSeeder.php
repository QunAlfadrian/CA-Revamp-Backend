<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use App\Models\Campaign;
use Illuminate\Support\Arr;
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
            $campaign = Campaign::factory()->count(1)->create([
                'organizer_id' => $user->id,
                'requested_fund_amount' => Arr::random([
                    3000000,
                    3500000,
                    4000000,
                    6000000,
                    7000000,
                    13000000,
                    15000000
                ]),
                'slug' => fake()->word()
            ]);
        }

        $user = User::whereHas('roleRelation', function ($q) {
            $q->where('name', 'organizer');
        })->first();
        $user->campaignsRelation()->create([
            'type' => 'product_donation',
            'title' => 'kyuib donasi',
            'slug' => 'kybdonasi',
            'description' => fake()->paragraphs(4, true),
            'header_image_url' => 'https://github.com/',
        ]);

        $prod = Campaign::where('slug', 'kybdonasi')->first();
        $prod->requestBook(
            Book::first(),
            5
        );
        $prod->update([
            'status' => 'on_progress'
        ]);

        $user = User::where('name', 'emi')->first();
        for ($i = 1; $i <= 5; $i++) {
            $campaign = Campaign::factory()->create([
                'organizer_id' => $user->id(),
                'requested_fund_amount' => Arr::random([
                    1000000, 3000000, 1500000
                ]),
                'slug' => 'temifund'.$i
            ]);
        }

        $campaigns = $user->campaigns();
        foreach ($campaigns as $campaign) {
            $campaign->update([
                'status' => 'on_progress'
            ]);
        }
    }
}
