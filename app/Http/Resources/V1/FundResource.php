<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class FundResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $hasDonor = $this->donation()->donor() ? true : false;
        $hasIdentity = $hasDonor && $this->donation()->donor()->identity() ? true : false;
        return [
            'type' => 'funds',
            'id' => $this->id(),
            'attributes' => [
                'amount' => $this->amount(),
                'updated_at' => $this->updatedAt()
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
            'status' => 'success',
        ];
    }

    public function withResponse(Request $request, JsonResponse $response) {
        $response->header('Accept', 'application/json');
    }
}
