<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductVariantResource;
use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\PackageResource;

class CartResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        $numberOfShippingDays = 5;
        $shippingDays = "Get by ".date(
            'D, d',
            strtotime(date('Y-m-d h:i:s'))
        ).' - '.date(
            'D d M Y',
            strtotime('+'.$numberOfShippingDays.' day', strtotime(date('Y-m-d h:i:s')))
        );
        return [
            'id'=> $this->id,
            'user'=> $this->user ? $this->user->first_name.' '.$this->user->last_name : null,
            'product'=> new ProductResource($this->product),
            'product_name'=> $this->product ? $this->product->name : null,
            'quantity'=> $this->quantity,
            'variant'=> $this->variant ? new ProductVariantResource($this->variant) : null,
            'standard_shipping_days' => $shippingDays,
            'created_at' => @$this->created_at->toIso8601String(),
        ];
    }
}
