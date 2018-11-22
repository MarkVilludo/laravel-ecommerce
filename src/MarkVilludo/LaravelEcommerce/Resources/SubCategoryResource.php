<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ChildSubCategoryResource;

class SubCategoryResource extends Resource
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
            'id' => $this->sub_category_id,
            'title' => $this->title,
            'description' => $this->description,
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'child_sub_categories' => ChildSubCategoryResource::collection($this->whenLoaded('childSubCategories')),
        ];
    }
}
