<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\OrderStatusResource;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\OrderStatus;
use Validator;
use Response;
use Config;

class OrderStatusController extends Controller
{

   
    //construction
    public function __construct(OrderStatus $orderStatus)
    {
        $this->orderStatus = $orderStatus;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orderStatus = $this->orderStatus->with('orders')->get();

        if ($orderStatus) {
            $data = OrderStatusResource::collection($orderStatus);
            return $data;
        } else {
            $data['message'] = 'There is no order status.';
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $rules = [
            'name' => 'required|unique:order_status,name'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $data['errors'] = [$validator->errors()];
            $statusCode = 422;
        } else {
            $newOrderStatus = new $this->orderStatus;
            $newOrderStatus->name = $request->name;
            $newOrderStatus->save();
            $data['message'] = config('app_messages.SuccessCreateOrderStatus');
            $statusCode = 200;
        }
        return Response::json(['data' => $data], $statusCode);
    }

    /**
     * Show orders by order status
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // return $id;
        $status = $this->orderStatus->where('id', $id)->with('orders')->first();

        if ($status) {
            $orderStatus = new OrderStatusResource($status);
            if (isset($orderStatus['orders'])) {
                $data['message'] = config('app_messages.ShowStatusOrders');
            } else {
                $data['message'] = config('app_messages.NotOrderInStatus');
            }
            $statusCode = 200;
            $data['status'] = $orderStatus;
        } else {
            $data['message'] = config('app_messages.NotFoundOrderStatus');
            $data['status'] = $status;
            $statusCode = 404;
        }
        return Response::json(['data' => $data], $statusCode);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return $id;
        $rules = [
            'name' => 'required|'.Rule::unique('order_status')->ignore($id, 'id')
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $data['message'] = [$validator->errors()];
            $statusCode = 422;
        } else {
            $orderStatus = $this->orderStatus->find($id);
            if ($orderStatus) {
                $orderStatus->name = $request->name;
                $orderStatus->save();

                $data['message'] = config('app_messages.SuccessUpdateOrderStatus');
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.NotFoundOrderStatus');
                $statusCode = 400;
            }
        }
        return Response::json(['data' => $data], $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // return $id;
        $orderStatus = $this->orderStatus->where('id', $id)->withTrashed()->first();

        if ($orderStatus) {
            if (!$orderStatus->deleted_at) {
                $orderStatus->delete();
                    $data['message'] = config('app_messages.SuccessDeleteOrderStatus');
                    $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.AlreadyDeletedOrderStatus');
                $statusCode = 400;
            }
        } else {
            $data['message'] = config('app_messages.NotFoundOrderStatus');
            $statusCode = 404;
        }
        return Response::json($data, $statusCode);
    }
}
