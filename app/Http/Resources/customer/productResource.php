<?php

namespace App\Http\Resources\customer;

use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class productResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'size' => $this->size,
            'color' => $this->color,
            'brand' => new BrandResource($this->brand),
            'first_category' => $this->first_category,
            'second_category' => new CategoryResource($this->category),
            'stock' => $this->stock,
            'image' => empty($this->image_url) ? null : url("storage/$this->image_url"),
            'promotion' => request()->routeIs(['promotions.show','promotions.index']) ? null : $this->promotions->filter(function ($promotion) {
            $now = now();
            return $now->between($promotion->start_date, $promotion->end_date);
        })->sortByDesc('created_at')->first(),
    ];;
    }
}
