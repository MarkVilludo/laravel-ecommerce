<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductImageResource;
use App\Http\Resources\ProductResource;

class FBTProductResource extends JsonResource
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
        $fileName = $this->product ?  ($this->product->defaultImage ? $this->product->defaultImage->file_name : '') :'';
        $DefImg = $this->product ? ($this->product->defaultImage ? url('/storage/products/'.$fileName) : '') :'';
        $MedImg = $this->product ? ($this->product->defaultImage ? url('/storage/products/medium/'.$fileName): '') :'';
        $SmImg = $this->product ? ($this->product->defaultImage ? url('/storage/products/small/'.$fileName): '') :'';
        $XsmImg = $this->product ? ($this->product->defaultImage ? url('/storage/products/xsmall/'.$fileName): '') :'';

        return [
            'id' => $this->id,
            'fbt_id' => $this->fbt_id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'product_id' => $this->product ? $this->product->id : null,
            'product_name' => $this->product ? $this->product->name: null,
            'regular_price' => $this->product ? $this->product->regular_price: null,
            'selling_price' => $this->product ? $this->product->selling_price : null,
            'default_image' => $DefImg,
            'medium_path' => $MedImg,
            'small_path' => $SmImg,
            'xsmall_path' => $XsmImg
        ];
    }
}
