<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Order\CancelOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\OrderStatus;
use App\Models\OrderNote;
use App\Models\OrderItem;
use App\Mail\OrderMail;
use App\Models\Order;
use Response;
use Config;

class OrderController extends Controller
{

    //construction model vairable
    public function __construct(Order $order, OrderItem $orderItem, OrderStatus $orderStatus, OrderNote $orderNote)
    {
        $this->order = $order;
        $this->orderStatus = $orderStatus;
        $this->orderNote = $orderNote;
        $this->orderItem = $orderItem;
    }
    /**
     * Display order listings
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $orders = $this->order->with('orderItems')->paginate(10);

        if ($orders) {
            $data['message'] = config('app_messages.ShowOrderList');
            $data =  OrderResource::collection($orders);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoOrdersAvailable');
            $statusCode = 200;
            return Response::json($data, $statusCode);
        }
        return $data;
    }
    //Search orders
    public function searchOrders(Request $request)
    {
        // return $request->all();
        $isReplacement = false;
        $orders = $this->order->withAndWhereHas('orderItems', function ($query) use ($request) {
            if ($request->status) {
                if ($request->status == config('setting.ReplacementStatus')) {
                    $isReplacement = true;
                } else {
                    $isReplacement = false;
                }
                
                if ($isReplacement) {
                    $query->where('is_replacement', true);
                } else {
                    $query->where('status_id', $request->status);
                }
            }
        })
                            ->getByOrderNumber($request->search)
                            ->paginate(10);

        if ($orders) {
            $data['message'] = config('app_messages.ShowOrderList');
            return $data['orders'] = OrderResource::collection($orders);
        } else {
            $data['message'] = config('app_messages.NoOrdersAvailable');
            $statusCode = 200;
            return Response::json($data, $statusCode);
        }
    }
    /**
     * Display customer order detaills
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerOrders(Request $request, $userId, $orderId)
    {
        // return $request->all();
        
        $customerOrders = $this->order::where('user_id', $userId)
                                        ->where('id', $orderId)
                                        ->with('user')
                                        ->with('notes')
                                        ->with('orderItems')
                                        ->first();

        if ($customerOrders) {
            $statusCode = 200;
            $data['orders'] = new OrderResource($customerOrders);
            $data['message'] = config('app_messages.CustomerOrderDetails');
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.OrderNotFound');
        }
        return Response::json($data, $statusCode);
    }

    /**
     * get customer orders (log user)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function myOrders(Request $request, $userId)
    {
        $user = $request->user('api');

        $userId = $userId ? $userId : $user->id;

        $myOrders =  $this->order->getByStatus($request->status)
                            ->orderBy('created_at', 'desc')
                            ->OwnOrders($userId)
                            ->get();

        if ($myOrders) {
            $statusCode = 200;
            return $data['orders'] = OrderResource::collection($myOrders);
            $data['message'] = config('app_messages.CustomerOrderDetails');
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.OrderNotFound');
        }
        return Response::json($data, $statusCode);
    }

    /**
     * get customer orders CMS
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customerOrdersCMS(Request $request, $userId)
    {
        $user = $request->user('api');

        $userId = $userId ? $userId : $user->id;
        $myOrders = $this->order->getByStatus($request->status)
                                    ->orderBy('created_at', 'desc')
                                    ->OwnOrders($userId)
                                    ->paginate(3);

        if ($myOrders) {
            $statusCode = 200;
            return $data['orders'] = OrderResource::collection($myOrders);
            $data['message'] = config('app_messages.CustomerOrderDetails');
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.OrderNotFound');
        }
        return Response::json($data, $statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($orderId)
    {
        // return $orderId;
        $order = $this->order->where('id', $orderId)->with('user')
                                        ->with('notes')
                                        ->with('orderItems.status')
                                        ->with('orderItems.notes')
                                        ->with('shippingAddress')
                                        ->with('billingAddress')
                                        ->first();

        if ($order) {
            $statusCode = 200;
            $data['order'] = new OrderResource($order);
            $data['message'] = config('app_messages.CustomerOrderDetails');
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.OrderNotFound');
        }

        return Response::json($data, $statusCode);
    }

    /**
     * Update each order status / customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CancelOrderRequest $request, $orderId)
    {
        $user = $request->user('api');
        $userId = $user->id;

        //check if user is existing in selected orders
        $order = $this->order->where('id', $orderId)
                            ->where('user_id', $userId)
                            ->first();

        if ($order) {
            $order->reason_for_cancellation = $request->reason;
            $order->date_cancelled = date('Y-m-d h:i:s');
            $order->update();

            // return $orderItemId;
            $orderItems = $this->orderItem->where('order_id', $orderId)->get();

            foreach ($orderItems as $key => $orderItem) {
                $orderItem->returnOrderItemQuantity($orderItem->id);
                $orderItem->status_id = 5;
                $orderItem->update();

                //order notes when update order status
                $newTransaction = new $this->orderNote;
                $newTransaction->order_id = $orderId;
                $newTransaction->order_item_id = $orderItem->id;
                $newTransaction->notes = config('app_messages.CancelledItemStatus');
                $newTransaction->save();
            }
            //send email after changed order item status
            //Get order details
            $order = $this->order->where('id', $orderId)
                            ->with('user.addresses')
                            ->with('notes')->with('orderItems.product')
                            ->first();

            Mail::to($order->user->email)->send(new OrderMail($order));

            //remove customer cache if any
            if (Cache::has('orders'.$userId)) {
                //clear cache products
                Cache::forget('orders'.$userId);
                //end clear cache
            }
            //end

            $statusCode = 200;
            $data['message'] = config('app_messages.CancelledOrderSuccess');
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.OrderNotFound');
        }
        // $data['user_id']  = $userId;
        // $data['order_id']  = $orderId;
      
        return Response::json($data, $statusCode);
    }
     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    /**
     * Update shipping days
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateShippingDays(Request $request)
    {
        // return $request->all();

        $orderItem = $this->orderItem->find($request->order_item_id);

        $createdDate = new \DateTime(date("Y-m-d", strtotime($orderItem->created_at)));
        $newShippingDays = new \DateTime($request->date);

        if ($newShippingDays >= $createdDate) {
            $interval = $createdDate->diff($newShippingDays);

            //get interval days
            $intervalDays =  $interval->format('%R%a');

            //replace '+' to null string (from +4 to '4')
            $daysToString = str_replace('+', '', $intervalDays);

            $orderItem->shipping_days = $daysToString;
            if ($orderItem->update()) {
                $statusCode = 200;
                $data['message'] = 'Successfully updated shipping days.';
            } else {
                $statusCode = 400;
                $data['message'] = 'Failed to update shipping days.';
            }
        } else {
            $statusCode = 400;
            $data['message'] = 'Please select days equals to order placed or greater than.';
        }

        return response()->json($data, $statusCode);
    }
    public function updateMultipleStatus(Request $request)
    {
            
        $orders = $request->orders;
        if ($orders) {
            foreach ($orders as $key => $order) {
                $order = $this->order->find($order['id']);
                $order->status_id = $request->status;
                $order->update();

                 // Send mail
                //Get order details
                $order = $this->order->where('id', $order->id)
                                ->with('user.defaultBillingAddress')
                                ->with('user.defaultShippingAddress')
                                ->with('notes')->with('orderItems.product')
                                ->with('orderItems.productImage')
                                ->first();
                                
                Mail::to($order->user->email)->send(new OrderMail($order));
            }
            $statusCode = 200;
            $data['message'] = 'Successfully updated order status.';
        } else {
            $statusCode = 400;
            $data['message'] = config('app_messages.SomethingWentWrong');
        }
        return Response::json($data, $statusCode);
    }
    /**
     * Get generated order number
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getOrderNumber(Request $request)
    {

        $user = $request->user('api');

        if ($user) {
            $userId = $user->id;

            $orderNumber = date('Ymdhis').''. str_pad($userId, 1, rand(2, 99), STR_PAD_LEFT).''.$userId;

            $statusCode = 200;
            $data['data'] = $orderNumber;
            $data['message'] = 'Shows generated order number.';
        } else {
            $statusCode = 400;
            $data['message'] = 'Please provide user token.';
        }

        return response()->json($data, $statusCode);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
