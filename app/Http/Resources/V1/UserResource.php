<?php

namespace App\Http\Resources\V1;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            $this->merge(Arr::except(parent::toArray($request), [
                'created_at',
                'updated_at',
                'email',
                'email_verified_at',
            ]))
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
