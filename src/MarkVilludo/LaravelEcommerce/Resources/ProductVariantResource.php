<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductVariantColorResource;
use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\ProductImageResource;

class ProductVariantResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'images' => ProductImageResource::collection($this->images),
            'colors' =>  ProductVariantColorResource::collection($this->colors),
            'inventory' => $this->inventory
        ];
    }
}
