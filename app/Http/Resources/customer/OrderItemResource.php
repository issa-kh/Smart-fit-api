<?php

namespace App\Http\Resources\customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'name' => $this->product->name,
            'price' => $this->price,
            'image' => empty($this->product->image_url) ? null : url("storage/".$this->product->image_url)
        ];
    }
}
