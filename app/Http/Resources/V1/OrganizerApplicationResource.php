<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizerApplicationResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'type' => 'organizer_applications',
            'id' => $this->id(),
            'attributes' => [
                'status' => $this->status(),
                'rejected_message' => $this->rejectedMessage(),
                'reviewed_at' => $this->reviewedAt()
            ],
            'relationships' => [
                'owner' => UserSummaryResource::make($this->applicant()),
                'reviewer' => UserSummaryResource::make($this->reviewer())
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
