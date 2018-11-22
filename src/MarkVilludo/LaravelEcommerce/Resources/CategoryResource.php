<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\SubCategoryResource;

class CategoryResource extends Resource
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
            'title' => $this->title,
            'description' => $this->description,
            'sub_categories' => SubCategoryResource::collection($this->whenLoaded('subCategories')),
            'created_at' => @$this->created_at->toIso8601String(),
            'updated_at' => @$this->updated_at
        ];
    }
}
