<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ProductImageResource extends Resource
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
            'product_id' => $this->product_id,
            'file_name' => $this->file_name,
            'original_path' => url('/storage/products/'.$this->file_name),
            'medium_path' => url('/storage/products/medium/'.$this->file_name),
            'small_path' => url('/storage/products/small/'.$this->file_name),
            'xsmall_path' => url('/storage/products/xsmall/'.$this->file_name),
            'page_preview' => $this->page_preview,
        ];
    }
}
