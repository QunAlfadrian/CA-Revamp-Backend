<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestedBookResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'type' => "requested_books",
            'id' => $this->id(),
            'attributes' => [
                'isbn' => $this->isbn(),
                'title' => $this->title(),
                'slug' => $this->slug(),
                'synopsis' => $this->synopsis(),
                'authors' => $this->authors(),
                'published_year' => $this->publishedYear(),
                'cover_image_url' => $this->coverImageUrl(),
                'price' => $this->price(),
                'requested_quantity' => $this->pivot->requested_quantity,
                'donated_quantity' => $this->pivot->donated_quantity
            ],
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
