<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\OrderNotesResource;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\OrderStatusResource;

class OrderResource extends Resource
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
        $checkIfCanCancell = 0;
        $countOrderItems = count($this->orderItems);
        if ($countOrderItems > 0) {
            foreach ($this->orderItems as $key => $orderItem) {
                if ($orderItem->status_id ==1) {
                    $checkIfCanCancell += 1;
                }
            }
        }

        return [
            'id' => $this->id,
            'user'=> new CustomerResource($this->user),
            'number'=> $this->number,
            'descriptions'=> $this->descriptions,
            // 'sub_total'=> $this->sub_total, //summary of computer regular price of products
            'sub_total'=> number_format($this->total_amount, 2), //summary of selling price
            'discount'=> $this->voucher_discount,
            'total_amount'=> number_format($this->grand_total, 2), //from total amount and voucher discount
            'shipping_fee'=> $this->shipping_fee,
            'promotions'=> $this->promotions,
            'shipping_status'=> $this->shipping_status,
            'payment_method'=> $this->remarks,
            'can_cancel' => $countOrderItems == $checkIfCanCancell ? true : false,
            'reason_for_cancellation'=> $this->reason_for_cancellation,
            'date_cancelled'=>  $this->date_cancelled ? date('d M Y h:i A', strtotime($this->date_cancelled)): null,
            'order_items'=> OrderItemResource::collection($this->whenLoaded('orderItems')),
            'shipping_address'=> new CustomerAddressResource($this->shippingAddress),
            'billing_address'=> new CustomerAddressResource($this->billingAddress),
            'transactions'=> $this->transactions,
            'placed_on' => date('d M Y h:i A', strtotime($this->created_at)),
            'placed_on_date' => date('d M Y', strtotime($this->created_at))
        ];
    }
}
