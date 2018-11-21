<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\OrderStatusResource;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\OrderStatus;
use Validator;
use Response;
use Config;
use Session;

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
        $data['orderStatus'] = $this->orderStatus->get();
        return view('admin.order_status.index', $data);
    }
 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.order_status.create');
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
            $message = $validator->errors();
            $statusCode = 422;
        } else {
            $newOrderStatus = new $this->orderStatus;
            $newOrderStatus->name = $request->name;
            $newOrderStatus->save();
            $message = config('app_messages.SuccessCreateOrderStatus');
            $statusCode = 200;
        }
        Session::flash('message', $message);
        return redirect()->back();
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
    public function edit($statusId)
    {
        //
        // return $statusId;
        $data['orderStatus'] = $this->orderStatus->find($statusId);

        return view('admin.order_status.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $statusId)
    {
        //
        // return $statusId;
          // return $id;
        $rules = [
            'name' => 'required|'.Rule::unique('order_status')->ignore($statusId, 'id')
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $message = $validator->errors();
            $statusCode = 422;
        } else {
            $orderStatus = $this->orderStatus->find($statusId);
            if ($orderStatus) {
                $orderStatus->name = $request->name;
                $orderStatus->save();

                $message = config('app_messages.SuccessUpdateOrderStatus');
                $statusCode = 200;
            } else {
                $message = config('app_messages.NotFoundOrderStatus');
                $statusCode = 400;
            }
        }
        Session::flash('message', $message);
        return redirect()->back();
    }

   /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($statusId)
    {
        // return $statusId;
        $orderStatus = $this->orderStatus->where('id', $statusId)->withTrashed()->first();

        if ($orderStatus) {
            if (!$orderStatus->deleted_at) {
                $orderStatus->delete();
                    $message = config('app_messages.SuccessDeleteOrderStatus');
                    $statusCode = 200;
            } else {
                $message = config('app_messages.AlreadyDeletedOrderStatus');
                $statusCode = 400;
            }
        } else {
            $message = config('app_messages.NotFoundOrderStatus');
            $statusCode = 404;
        }
        Session::flash('message', $message);
        return redirect()->back();
    }
}
