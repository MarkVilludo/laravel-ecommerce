<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class OrderNotesResource extends Resource
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
            'order_id' => $this->order_id,
            'order_item_id' => $this->order_item_id,
            'notes' => $this->notes,
            'created_at' => date('d M Y h:i A', strtotime($this->created_at))
        ];
    }
}
