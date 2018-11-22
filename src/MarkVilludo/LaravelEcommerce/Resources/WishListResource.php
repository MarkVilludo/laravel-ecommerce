<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductVariantResource;
use Illuminate\Http\Resources\Json\Resource;

class WishListResource extends Resource
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
            'id'=> $this->id,
            'variant'=> $this->variant ? new ProductVariantResource($this->variant) : null,
            'product'=> new ProductResource($this->product),
            'customer' => new UserResource($this->customer),
            'quantity'=> $this->quantity,
            'created_at' => date("M j, Y G:ia", strtotime($this->created_at))
        ];
    }
}
