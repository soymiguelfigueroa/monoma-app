<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LeadsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = [
            'meta' => [
                'success'=> true,
                'errors'=> []
            ],
            'data' => []
        ];

        foreach ($this->collection as $item) {
            $response['data'][] = [
                'id' => $item->id,
                'name' => $item->name,
                'source' => $item->source,
                'owner' => $item->owner,
                'created_at' => $item->created_at,
                'created_by' => $item->created_by
            ];
        }

        return $response;
    }
}
