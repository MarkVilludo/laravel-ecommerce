<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'user_id' => $this->user_id,
            'promo_id' => $this->promo_id,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => @$this->created_at->toIso8601String(),
            'updated_at' => @$this->updated_at->toIso8601String()
        ];
    }
}
