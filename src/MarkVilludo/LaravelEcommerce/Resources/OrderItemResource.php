<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\OrderNotesResource;
use App\Http\Resources\ProductResource;

class OrderItemResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $shippingDays = "Get by ".date(
            'D, d',
            strtotime($this->created_at)
        ).' - '.date(
            'D d M Y',
            strtotime('+'.$this->shipping_days.' day', strtotime($this->created_at))
        );
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'item' => $this->item,
            'short_name' => str_limit($this->item, 18),
            'status'=> $this->status,
            'is_replacement'=> $this->is_replacement ? true : false,
            'status_date'=> $this->status->id != 1 ? $this->status->name.' by '.date(
                'd M Y h:i A',
                strtotime($this->updated_at)
            ) :'',
            'shipping_days'=> $this->shipping_days,
            'standard_shipping_days'=> $shippingDays,
            'notes'=> $this->notes ? OrderNotesResource::collection($this->notes): [],
            'remarks'=> $this->remarks,
            'date_replaced'=> 'For replacement by '.date('d M Y h:i A', strtotime($this->date_replaced)),
            'small_path_default_image' => url('/storage/products/small/'.$this->product->defaultImage->file_name),
            'medium_path' => url('/storage/products/medium/'.$this->product->defaultImage->file_name),
            'xsmall_path' => url('/storage/products/xsmall/'.$this->product->defaultImage->file_name),
            'original_path' => url('/storage/products/'.$this->product->defaultImage->file_name),
            'variant_id' => $this->variant_id,
            'regular_price' => $this->regular_price,
            'selling_price' => $this->selling_price,
            'total' => number_format($this->total, 2),
            'discount' => $this->discount,
            'quantity' => $this->quantity,
            'created_at' => @$this->created_at->toIso8601String(),
            'updated_at' => @$this->updated_at->toIso8601String()
        ];
    }
}
