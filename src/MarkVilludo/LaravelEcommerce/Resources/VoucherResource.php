<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProductResource;

class VoucherResource extends Resource
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
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'uses' => $this->uses,
            'max_uses' => $this->max_uses,
            'max_uses_user' => $this->max_uses_user,
            'type' => $this->type,
            'discount_amount' => $this->discount_amount,
            'is_fixed' => $this->is_fixed,
            'is_enabled' => $this->is_enabled,
            'starts_at' => $this->start_datetime,
            'expires_at' => $this->expiry_datetime,
            'status' => $this->status,
            'model_name' => $this->model_name,
            'max_amt_cap' => $this->max_amt_cap,
            'min_amt_availability' => $this->min_amt_availability,
            // 'users' => UserResource::collection($this->whenLoaded('users')),
            // 'product' => ProductResource::collection($this->whenLoaded('product')),
            // 'user' => UserResource::collection($this->whenLoaded('user')),
            'created_at' => $this->created_at ? @$this->created_at->toIso8601String(): null,
            'updated_at' => $this->updated_at ? @$this->updated_at->toIso8601String(): null
        ];
    }
}
