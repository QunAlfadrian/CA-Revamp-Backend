<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class IdentityResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'type' => 'identities',
            'id' => $this->id(),
            'attributes' => [
                'full_name' => $this->fullName(),
                'phone_number' => $this->phoneNumber(),
                'gender' => $this->gender(),
                'profile_image_url' => $this->profileImageUrl(),
                'date_of_birth' => $this->dateOfBirth(),
                'nik' => $this->nik(),
                'id_card_image_url' => $this->idCardImageUrl()
            ],
            'relationships' => [
                'owner' => UserResource::make($this->user()),
            ],
            'links' => [
                'self' => route('identities.show')
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
