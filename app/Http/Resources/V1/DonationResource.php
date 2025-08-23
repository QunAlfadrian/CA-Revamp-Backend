<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $campaign = $this->campaign();
        return [
            'type' => 'donations',
            'id' => $this->id(),
            'attributes' => [
                'type' => $this->type(),
                'verified_at' => $this->verifiedAt()
            ],
            'relationships' => [
                'donor' => $this->donor()
                    ? UserSummaryResource::make($this->donor())
                    : $this->donorName(),
                'campaign' => [
                    'type' => 'campaigns',
                    'id' => $campaign->id(),
                    'attributes' => [
                        'title' => $campaign->title(),
                        'slug' => $campaign->slug()
                    ]
                ]
            ],
            'links' => [

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
