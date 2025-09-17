<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'type' => 'campaigns',
            'id' => $this->id(),
            'attributes' => [
                'campaign_type' => $this->type(),
                'title' => $this->title(),
                'slug' => $this->slug(),
                'description' => $this->description(),
                'header_image_url' => $this->headerImageUrl(),
                'status' => $this->status(),
                'created_at' => $this->createdAt(),
                'requested_fund_amount' => $this->requestedFund(),
                'donated_fund_amount' => $this->donatedFund(),
                'requested_item_quantity' => $this->requestedItemQuantity(),
                'donated_item_quantity' => $this->donatedItemQuantity(),
                'reviewed_at' => $this->reviewedAt()
            ],
            'relationships' => [
                'organizer' => UserSummaryResource::make($this->organizer()),
                'reviewer' => UserSummaryResource::make($this->reviewer()),
                'requested_items' => $this->type() == 'product_donation' ? [
                    'books' => RequestedBookResource::collection($this->books()),
                    'supplies' => SupplyResource::collection($this->requestedSupplies())
                ] : null
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
