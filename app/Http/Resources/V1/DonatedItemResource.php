<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class DonatedItemResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'type' => 'donated_items',
            'id' => $this->id(),
            'attributes' => [
                'quantity' => $this->quantity(),
                'package_picture_url' => $this->packagePictureUrl(),
                'delivery_service' => $this->deliveryService(),
                'resi' => $this->resi(),
                'status' => $this->status()
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
