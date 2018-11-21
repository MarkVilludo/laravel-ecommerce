<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\Ordertag;
use Validator;
use Response;
use Config;
use Session;

class OrderTagsController extends Controller
{

   
    //construction
    public function __construct(Ordertag $orderTag)
    {
        $this->orderTag = $orderTag;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['orderTags'] = $this->orderTag->paginate(10);

        return view('admin.order_tag.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.order_tag.create');
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
        $rules = [
            'name' => 'required|unique:order_tag,name'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $message = $validator->errors();
            $statusCode = 422;
        } else {
            $newOrderStatus = new $this->orderTag;
            $newOrderStatus->name = $request->name;
            $newOrderStatus->save();
            $message = config('app_messages.SuccessCreateOrderTag');
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
    public function edit($tagId)
    {
        //
        // return $id;
        $data['orderTag'] = $this->orderTag->find($tagId);

        return view('admin.order_tag.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tagId)
    {
        //
        $rules = [
            'name' => 'required|'.Rule::unique('order_tag')->ignore($tagId, 'id')
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $message = $validator->errors();
            $statusCode = 422;
        } else {
            $orderTag = $this->orderTag->find($tagId);
            if ($orderTag) {
                $orderTag->name = $request->name;
                $orderTag->update();

                $message = config('app_messages.SuccessUpdateOrderTag');
                $statusCode = 200;
            } else {
                $message = config('app_messages.NotFoundOrderTag');
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
    public function destroy($tagId)
    {
        // return $tagId;
        $orderTag = $this->orderTag->where('id', $tagId)->withTrashed()->first();

        if ($orderTag) {
            if (!$orderTag->deleted_at) {
                $orderTag->delete();
                    $message = config('app_messages.SuccessDeleteOrderTag');
                    $statusCode = 200;
            } else {
                $message = config('app_messages.AlreadyDeletedOrderTag');
                $statusCode = 400;
            }
        } else {
            $message = config('app_messages.NotFoundOrderTag');
            $statusCode = 404;
        }
        Session::flash('message', $message);
        return redirect()->back();
    }
}
