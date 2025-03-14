<?php

namespace App\Http\Resources\customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'shipping_information' => [
                'shipping_address' => $this->shipping_address,
                'city' => $this->city,
                'state' => $this->state,
                'zip_code' => $this->zip_code,
                'phone_number' => $this->phone_number
            ],
            'order_items' => OrderItemResource::collection($this->OrderItems)
        ];
    }
}
