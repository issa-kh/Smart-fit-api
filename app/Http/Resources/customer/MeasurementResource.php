<?php

namespace App\Http\Resources\customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeasurementResource extends JsonResource
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
            'height' => $this->height,
            'weight' => $this->weight,
            'chest' => $this->chest,
            'waist' => $this->waist,
            'gender' => $this->gender,
            'customer_id' => $this->user_id
        ];
    }
}
