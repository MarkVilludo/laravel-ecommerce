<!DOCTYPE html>
<html>
<head>
    <title>Orders</title>
</head>
<body>
<br/>
Your order details :
<p> Dear <b> {{$order->user->first_name.' '.$order->user->last_name}}</b></p>
<br>
<span> <b>Your Order # {{$order->number}} has been placed on {{date( "l, F d, Y", strtotime( $order->created_at) )}} via standard shipping method.  </span>
<br>
<p>Note: Special noted for fashion 21</p>

<span>We will send you another email confirming the shipping of your order.</span>

<p>Order details</p>
<span>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
</span>
<br>
<br>
<fieldset>
	@foreach($order->orderItems as $orderItem)
		<div class="col-lg-12">
			<div class="col-lg-3">
				@if($orderItem->productImage)
					<img src="{{ $message->embed($orderItem->productImage->path) }}">
				@endif
			</div>
			<div class="col-lg-3">
				{{$orderItem->product['name']}}
				<br>
				Quantity: {{$orderItem->quantity}}
			</div>
			<div class="col-lg-3">
				{{$orderItem->selling_price}}
			</div>
		</div>
	@endforeach	
</fieldset>
<fieldset>
	 <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="portlet">
                <div id="bg-default" class="panel-collapse">
                    <div class="portlet-body">
                        <h4 class="portlet-title text-dark">
                            Total Summary
                        </h4>
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <p><span>Subtotal</span></p>
                                <p><span>Shipping Fees</span></p>
                                <p><span>Promotions</span></p>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <p><span> </span>
                                     <label style="text-decoration: line-through;"> Php {{$order->total_amount}} </label> Php {{$order->grand_total}}
                                        <br>
                                    </p>
                                <p><span> Php {{$order->shipping_fee}}</span></p>
                                <p><span> Php 0.00</span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <p><span>Discount</span></p>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <p><span> Php {{$order->discount + $order->voucher_discount}}</span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <p><span>Total (VAT Applied where applicable)</span></p>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <p><span> Php {{$order->grand_total}}</span></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</fieldset>
</body>
 
</html>