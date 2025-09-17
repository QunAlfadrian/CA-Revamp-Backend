<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class DonatedItemSummaryResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $hasDonor = $this->donation()->donor() ? true : false;
        $hasIdentity = $hasDonor && $this->donation()->donor()->identity() ? true : false;
        return [
            'type' => 'donated_items',
            'id' => $this->id(),
            'attributes' => [
                'quantity' => $this->quantity(),
                'updated_at' => $this->updated_at
            ],
            'donor' => [
                'name' => $this->donation()->donorName(),
                'profile_image_url' => $hasDonor && $hasIdentity
                ? $this->donation()->donor()->identity()->profileImageUrl()
                : null
            ]
        ];
    }

    public function with($request) {
        return [
            'status' => 'success'
        ];
    }

    public function withResponse(Request $request, JsonResponse $response) {
        $response->header('Accept', 'application/json');
    }
}
