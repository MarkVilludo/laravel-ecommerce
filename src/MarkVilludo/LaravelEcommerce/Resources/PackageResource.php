<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

use App\Http\Resources\PackageItemResource;

class PackageResource extends Resource
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
            'name' => $this->name,
            'description' => $this->description,
            'child_category_id' => $this->child_sub_category_id,
            'category' => $this->category ? $this->category->title : null,
            'warranty' => $this->warranty,
            'warranty_type' => $this->warranty_type,
            'items' => PackageItemResource::collection($this->items),
            'status' => $this->status,
            'price' => $this->price,
            'created_at' => @$this->created_at->toIso8601String(),
            'updated_at' => @$this->updated_at->toIso8601String()
        ];
    }
}
