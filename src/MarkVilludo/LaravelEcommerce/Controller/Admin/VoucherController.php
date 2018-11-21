<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Api\Vouchers\VoucherRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Voucher;

class VoucherController extends Controller
{
    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // //get vouchers not expired and available
        // $data['dateTimeToday'] = date('Y-m-d H:i:s');
        // // where('expires_at','>', $dateTime)
        // $data['vouchers'] = Voucher::orderBy('created_at', 'DESC')->paginate(10);
        //get data from api
        return view('admin.voucher.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.voucher.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VoucherRequest $request)
    {
        $starts_at = date('Y-m-d h:i:s', strtotime(request('start_date').' '.request('start_time')));
        $expires_at = date('Y-m-d h:i:s', strtotime(request('expiry_date').' '.request('expiry_time')));
        // check if the code is already existing with the same promo date validation
        $checkVoucher = $this->voucher->checkExistence($request->code, $starts_at, $expires_at)->get();
        if ($checkVoucher->count() > 0) {
            $data['message'] = config('app_messages.FailVoucherAlreadyExist');
            $data['status'] = 'failed';
            $code = 409;
        } else {
            $newVoucher = new $this->voucher;
            $newVoucher->code = request('code');
            $newVoucher->name = request('name');
            $newVoucher->description = request('description');
            $newVoucher->max_uses = request('max_uses');
            $newVoucher->max_uses_user = request('max_uses_user');
            $newVoucher->type = '1';
            $newVoucher->discount_amount = request('discount_amount');
            $newVoucher->is_fixed = request('is_fixed') == true ? 1 : 0;
            $newVoucher->starts_at = $starts_at;
            $newVoucher->expires_at = $expires_at;
            $newVoucher->max_amt_cap = request('max_amt_cap');
            $newVoucher->min_amt_availability = request('min_amt_availability');
            $newVoucher->is_enabled = request('is_enabled') == true ? 1 : 0;
            $newVoucher = $newVoucher->save();


            $data['message'] = config('app_messages.SuccessVoucherCreated');
            $data['status'] = 'ok';
            $data['redirect'] = route('vouchers.index');
            $code = 201;
            // $data['data'] = new VoucherResource($newVoucher);
        }

        return response()->json($data, $code);
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
        $data['item'] = Voucher::find($id);
        if ($data['item']) {
            return view('admin.voucher.edit', $data);
        }
        return abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VoucherRequest $request, $id)
    {
        // return $request->all();
        // return $request->item_id;
        $starts_at = date('Y-m-d h:i:s', strtotime(request('start_date').' '.request('start_time')));
        $expires_at = date('Y-m-d h:i:s', strtotime(request('expiry_date').' '.request('expiry_time')));


         $voucher = $this->voucher->find($id);
        if ($voucher) {
             // return 'test';
            // check if the code is already existing with the same promo date validation
            $checkVoucher = $this->voucher->where('id', '!=', $id)
                                          ->checkExistence($request->code, $starts_at, $expires_at)
                                          ->get();
                                          
            if ($checkVoucher->count() > 0) {
                $data['message'] = config('app_messages.FailVoucherAlreadyExist');
                $data['status'] = 'failed';
                $code = 409;
            } else {
                $voucher = $this->voucher->find($id);
                $voucher->code = request('code');
                $voucher->name = request('name');
                $voucher->description = request('description');
                $voucher->max_uses = request('max_uses');
                $voucher->max_uses_user = request('max_uses_user');
                $voucher->type = '1';
                $voucher->discount_amount = request('discount_amount');
                $voucher->is_fixed = request('is_fixed') == true ? 1 : 0;
                $voucher->starts_at = $starts_at;
                $voucher->expires_at = $expires_at ;
                $voucher->max_amt_cap = request('max_amt_cap');
                $voucher->min_amt_availability = request('min_amt_availability');
                $voucher->is_enabled = request('is_enabled') == true ? 1 : 0;
                $voucher->save();

                $data['message'] = config('app_messages.SuccessVoucherUpdated');
                $data['status'] = 'ok';
                $code = 200;
                $data['redirect'] = route('vouchers.index');
            }
            return response()->json($data, $code);
        } else {
            $code = 204;
            $data['message'] = config('app_messages.FailVoucherNotFound');
            $data['status'] = 'failed';
            return response()->json($data, $code);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $request->item_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $checkExistence = $this->voucher->find($id);

        if ($checkExistence) {
            $checkExistence->delete();
            $data['message'] = config('app_messages.SuccessVoucherDestroyed');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        } else {
            return  abort(204);
        }
    }
}
