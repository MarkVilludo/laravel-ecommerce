<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\ChildSubCategoryResource;
use App\Http\Resources\ProductVariantResource;
use App\Http\Resources\ProductImageResource;
use App\Http\Resources\ProductFaqResource;
use App\Http\Resources\FBTProductResource;
use App\Http\Resources\ReviewsResource;

class ProductMostPicks extends Resource
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
            'name'=> $this->name,
            'short_name' => str_limit($this->name, 18),
            'regular_price'=> $this->regular_price,
            'selling_price'=> $this->selling_price,
            'featured'=> $this->featured,
            'category'=> $this->category ? $this->category->title : '',
            'variants' => $this->variants ? ProductVariantResource::collection($this->variants): [],
            'default_image' => $this->defaultImage ? url('/storage/products/'.$this->defaultImage->file_name) :'',
            'created_at' => @$this->created_at->toIso8601String(),
            'updated_at' => @$this->updated_at->toIso8601String()
        ];
    }
}
