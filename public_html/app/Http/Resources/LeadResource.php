<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'meta' => [
                'success' => true,
                'errors' => []
            ],
            'data' => [
                'id' => $this->id,
                'name' => $this->name,
                'source' => $this->source,
                'owner' => $this->owner,
                'created_at' => $this->created_at,
                'created_by' => $this->created_by
            ]
        ];
    }
}
