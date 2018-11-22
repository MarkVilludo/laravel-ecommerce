<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\ProductResource;

class ChildSubCategoryResource extends Resource
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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'short_description' => str_limit($this->description, 40),
            'featured_product' => new ProductResource($this->whenLoaded('featuredProduct')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'file_name' => $this->file_name,
            'original_path' => url('/storage/categories/'.$this->file_name),
            'medium_path' => url('/storage/categories/medium/'.$this->file_name),
            'small_path' => url('/storage/categories/small/'.$this->file_name),
            'xsmall_path' => url('/storage/categories/xsmall/'.$this->file_name)

        ];
    }
}
