<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecentlyViewResource extends JsonResource
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
        $fileName = $this->product->defaultImage ? $this->product->defaultImage->file_name :'';

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'short_name' => str_limit($this->product->name, 18),
            'regular_price' => $this->product->regular_price,
            'selling_price' => $this->product->selling_price,
            'default_image' => $this->product->defaultImage ? url('/storage/products/'.$fileName) :'',
            'medium_path' => $this->product->defaultImage ? url('/storage/products/medium/'.$fileName) :'',
            'small_path' => $this->product->defaultImage ? url('/storage/products/small/'.$fileName) :'',
            'xsmall_path' => $this->product->defaultImage ? url('/storage/products/xsmall/'.$fileName) :'',
            
        ];
    }
}
