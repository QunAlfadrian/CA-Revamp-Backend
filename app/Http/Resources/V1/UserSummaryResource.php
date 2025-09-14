<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSummaryResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'type' => 'users',
            'id' => $this->id(),
            'attributes' => [
                'name' => $this->name(),
                'profile_image_url' => $this->identity()
                    ? $this->identity()->profileImageUrl()
                    : ""
            ]
        ];
    }
}
