<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            'organizer_id' => $attribute['organizer_id'] ?? User::factory(),
            'type' => $attribute['type'] ?? 'fundraiser',
            'title' => $this->faker->sentence(5),
            'slug' => $attribute['slug'] ?? 'campaign-' . Carbon::now()->format('dmYHis'),
            'description' => $this->faker->paragraph(15),
            'header_image_url' => 'https://arkwaifu.cc/api/v1/arts/pic_rogue_1_16/variants/origin/content',
            'status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null
        ];
    }
}
