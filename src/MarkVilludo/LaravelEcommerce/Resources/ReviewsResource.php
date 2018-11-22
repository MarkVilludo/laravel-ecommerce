<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProductResource;

class ReviewsResource extends Resource
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
        //get product default images
        $defaultImage = $this->product->defaultImage ? $this->product->defaultImage->file_name : null;

        return [
            'id' => $this->id,
            'rate' => $this->rate,
            'title' => $this->title,
            'description' => $this->description,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'original_path' => url('/storage/products/'.$defaultImage),
            'medium_path' => url('/storage/products/medium/'.$defaultImage),
            'small_path' => url('/storage/products/small/'.$defaultImage),
            'xsmall_path' => url('/storage/products/xsmall/'.$defaultImage),
            'user' => $this->user,
            'date' =>  date("j M Y", strtotime($this->created_at))
        ];
    }
}
