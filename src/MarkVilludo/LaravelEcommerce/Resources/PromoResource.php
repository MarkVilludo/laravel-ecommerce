<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PromoResource extends JsonResource
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
            'short_description' => str_limit($this->description, 200),
            'description' => $this->description,
            'date' => date("M j", strtotime($this->start_date)).' to '.date("M j, Y", strtotime($this->end_date)),
            'original_path' => url('/storage/promos/'.$this->file_name),
            'medium_path' => url('/storage/promos/medium/'.$this->file_name),
            'small_path' => url('/storage/promos/small/'.$this->file_name),
            'xsmall_path' => url('/storage/promos/xsmall/'.$this->file_name),
            'created_at' => @$this->created_at->toIso8601String(),
            'updated_at' => @$this->updated_at->toIso8601String()
        ];
    }
}
