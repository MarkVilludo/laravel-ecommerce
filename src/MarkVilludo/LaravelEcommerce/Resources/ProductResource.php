<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\ChildSubCategoryResource;
use App\Http\Resources\ProductVariantResource;
use App\Http\Resources\ProductImageResource;
use App\Http\Resources\ProductInfoResource;
use App\Http\Resources\ProductFaqResource;
use App\Http\Resources\FBTProductResource;
use App\Http\Resources\ReviewsResource;

class ProductResource extends Resource
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
            'category'=> new ChildSubCategoryResource($this->category),
            'variants' => $this->variants ? ProductVariantResource::collection($this->variants): [],
            'images' => $this->latestImage ? ProductImageResource::collection($this->latestImage): [],
            'default_image' => $this->defaultImage ? url('/storage/products/'.$this->defaultImage->file_name) :'',
            'informations' => $this->infos ? ProductInfoResource::collection($this->infos): [],
            'fbt_id' => $this->fbt_id,
            'reviews' => ReviewsResource::collection($this->whenLoaded('reviews')),
            'average_ratings' => round($this->ratings),
            'brand_id'=> $this->brand_id,
            'short_description'=> str_limit($this->short_description, 40),
            'description'=> $this->description,
            'warranty_type'=> $this->warranty_type,
            'warranty'=> $this->warranty,
            'model'=> $this->model,
            'regular_price'=> $this->regular_price,
            'selling_price'=> $this->selling_price,
            'url'=> $this->url,
            'is_new_arrival'=> $this->is_new_arrival,
            'featured'=> $this->featured,
            'status'=> $this->status,
            'views'=> $this->views,
            'manual'=> $this->manual,
            'created_at' => @$this->created_at->toIso8601String(),
            'updated_at' => @$this->updated_at->toIso8601String()
        ];
    }
}
