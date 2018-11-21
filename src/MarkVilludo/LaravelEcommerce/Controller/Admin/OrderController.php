<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Models\OrderNote;
use App\Models\OrderStatus;
use App\Models\OrderItem;
use App\Mail\OrderMail;
use Response;
use Config;
use Session;

class OrderController extends Controller
{

   
    //construction model vairable
    public function __construct(
        Order $order,
        OrderNote $orderNote,
        OrderStatus $orderStatus,
        OrderItem $orderItem
    ) {
        $this->order = $order;
        $this->orderNote = $orderNote;
        $this->orderStatus = $orderStatus;
        $this->orderItem = $orderItem;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Get all orders sort by newest order
        // Get data thru api to minimize codes
        $data['orders'] = OrderResource::collection($this->order->paginate(10));

        return view('admin.order.index', $data);
    }
    public function customerOrderDetails($customerId, $orderId)
    {
        //get data from api
        $data['customerId'] =  $customerId;
        $data['orderId'] =  $orderId;

        return view('admin.customer.order.details', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    /**
     * Create order notes for each order
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customerOrderCreateNote(Request $request, $customerId, $orderId, $orderItemId)
    {
        //
        // return $orderId;
        $order = $this->order->find($orderId);
        $data['customer_id'] = $customerId;
        $data['order'] = $order;
        $data['order_item_id'] = $orderItemId;

            
        return view('admin.customer.order.notes.create', $data);
    }

    //Store
    public function customerOrderStoreNote(Request $request)
    {
        // return $request->all();
        $checkNoteExist = $this->orderNote->where('id', $request->order_id)
                                ->where('notes', $request->note)
                                ->first();

        if ($checkNoteExist) {
            $message  = config('app_messages.InvalidExistingOrderNote');
        } else {
            $newOrderNote = new $this->orderNote;
            $newOrderNote->order_id = $request->order_id;
            $newOrderNote->order_item_id = $request->order_item_id;
            $newOrderNote->notes = $request->note;
            
            if ($newOrderNote->save()) {
                $message  = config('app_messages.SuccessCreateOrderNote');
            } else {
                $message  = config('app_messages.SomethingWentWrong');
            }
        }

        Session::flash('message', $message);
        return redirect()->back();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update each order status / admin or cms side
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $orderId, $orderItemId)
    {
        
        // return $orderItemId;
        $orderItem = $this->orderItem->where('id', $orderItemId)
                                    ->where('order_id', $orderId)
                                    ->first();

        if ($orderItem) {
            $statusId = $request->status_id;
            $transactionNotes = '';
            //check if select to cancel or refund order item
            if ($request->status_id == config('setting.ReplacementStatus')) {
                $transactionNotes = config('app_messages.ReplacementItemStatus');
                $statusId = 1;
                $orderItem->is_replacement = true;
                $orderItem->remarks = 'Defective/Does not work properly.';
                $orderItem->date_replaced = date('Y-m-d h:i:s');
            } elseif ($request->status_id == config('setting.CancelledReturnStatus')) {
                $transactionNotes = config('app_messages.CancelledItemStatus');
                //return stocks from orders
                $this->orderItem->returnOrderItemQuantity($orderItemId);
                //end return stocks
            } elseif ($request->status_id == config('setting.CompletedStatus')) {
                $transactionNotes = config('app_messages.CompleteOrderItemStatus');
            } elseif ($request->status_id == config('setting.DeliveredStatus')) {
                $transactionNotes = config('app_messages.DeliveredItemStatus');
            } elseif ($request->status_id == config('setting.ShippedStatus')) {
                $transactionNotes = config('app_messages.ShippedItemStatus');
            }
            $orderItem->status_id = $statusId;

            $getorderStatus = $this->orderStatus->find($orderItem->status_id);

            $finalNote = $transactionNotes ? $transactionNotes: $getorderStatus->name;

            //order notes when update order status
            $newTransaction = new $this->orderNote;
            $newTransaction->order_id = $orderId;
            $newTransaction->order_item_id = $orderItemId;
            $newTransaction->notes = $request->reason ? $request->reason : $finalNote;
            $newTransaction->save();

            

            if ($orderItem->update()) {
                //send email after changed order item status
                //Get order details
                $order = $this->order->where('id', $orderId)
                                ->with('user.addresses')
                                ->with('notes')->with('orderItems.product')
                                ->first();

                Mail::to($order->user->email)->send(new OrderMail($order));

                $statusCode = 200;
                $data['message'] = config('app_messages.SuccessUpdateOrderItemStatus');
            } else {
                $statusCode = 400;
                $data['message'] = config('app_messages.SomethingWentWrong');
            }
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.OrderNotFound');
        }
       
        return Response::json($data, $statusCode);
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
