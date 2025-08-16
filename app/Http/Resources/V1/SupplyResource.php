<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplyResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'type' => 'requested_supplies',
            'id' => $this->id(),
            'attributes' => [
                'name' => $this->name(),
                'description' => $this->description(),
                'price' => $this->price(),
                'requested_quantity' => $this->requestedQuantity(),
                'donated_quantity' => $this->donatedQuantity(),
                'status' => $this->status(),
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
