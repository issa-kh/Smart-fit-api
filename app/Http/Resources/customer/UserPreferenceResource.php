<?php

namespace App\Http\Resources\customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'colors' => $this->colors,
            'brands' => $this->brands,
            'customer_id' => $this->user_id
        ];
    }
}
