<?php

namespace Database\Seeders;

use App\Models\Book;
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

        $user = User::whereHas('roleRelation', function ($q) {
            $q->where('name', 'organizer');
        })->first();
        $user->campaignsRelation()->create([
            'type' => 'product_donation',
            'title' => 'Test Product Donation',
            'slug' => 'kybdonasi',
            'description' => fake()->paragraphs(4, true),
            'header_image_url' => 'https://github.com/',
        ]);

        $prod = Campaign::where('slug', 'kybdonasi')->first();
        $prod->requestBook(
            Book::first(),
            5
        );
    }
}
