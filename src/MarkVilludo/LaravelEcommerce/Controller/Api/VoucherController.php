<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Vouchers\VoucherRequest as VoucherRequest;
use App\Http\Resources\VoucherResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\VoucherTrait;
use App\Models\VoucherUsers;
use App\Models\Voucher;
use \Carbon\Carbon;
use Validator;
use Response;
use Config;

class VoucherController extends Controller
{
    use VoucherTrait;

    public function __construct(Voucher $voucher, VoucherUsers $voucherUser)
    {
        $this->voucher = $voucher;
        $this->voucherUser = $voucherUser;
    }

    /**
    * Display voucher listings
    *
    * @return Object
    */

    public function index(Request $request)
    {
        
        $vouchers = $this->voucher->paginate(10);

        if ($vouchers) {
            $data = VoucherResource::collection($vouchers);
            return $data;
        } else {
            $data['message'] = 'There is no voucher available.';
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
        // $collections = VoucherResource::collection($vouchers);
        // $data['data'] = paginateCollection($collections);
    }
    public function searchVoucher(Request $request)
    {
        $vouchers = $this->voucher->searchColumns($request->searchBy, $request->searchString)
                                        ->SearchByDateRange($request->start_date, $request->end_date)
                                        ->paginate(10);

        if ($vouchers) {
            $data = VoucherResource::collection($vouchers);
            return $data;
        } else {
            $data['message'] = 'There is no voucher available.';
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  App\Http\Requests\Api\Vouchers\VoucherRequest  $request
    * @return object
    */
    public function store(VoucherRequest $request)
    {
        // check if the code is already existing with the same promo date validation
        $checkVoucher = $this->voucher->checkExistence($request->code, $request->starts_at, $request->expires_at)
                                      ->get();

        if ($checkVoucher->count() > 0) {
            $data['message'] = config('app_messages.FailVoucherAlreadyExist');
            $data['status'] = 409;
        } else {
            $startsAt = date('Y-m-d H:i:s', strtotime(request('starts_date').' '.request('starts_date')));
            $expiresAt = date('Y-m-d H:i:s', strtotime(request('expires_date').' '.request('expires_time')));

            $newVoucher = new $this->voucher;
            $newVoucher->code = request('code');
            $newVoucher->name = request('name');
            $newVoucher->description = request('description');
            $newVoucher->max_uses = request('max_uses');
            $newVoucher->max_uses_user = request('max_uses_user');
            $newVoucher->type = '1';
            $newVoucher->discount_amount = request('discount_amount');
            $newVoucher->is_fixed = request('is_fixed');
            $newVoucher->starts_at = $startsAt;
            $newVoucher->expires_at = $expiredAt;
            $newVoucher->max_amt_cap = request('max_amt_cap');
            $newVoucher->min_amt_availability = request('min_amt_availability');
            $newVoucher->is_enabled = request('is_enabled');
            $newVoucher = $newVoucher->save();


            $data['message'] = config('app_messages.SuccessVoucherCreated');
            $data['status'] = 201;
            $data['data'] = new VoucherResource($newVoucher);
        }

        return Response::json($data, $data['status']);
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        $voucher = $this->voucher->find($id);

        if ($voucher) {
            $data['data'] = new VoucherResource($voucher);
            $data['status'] = 200;
            return Response::json($data, $data['status']);
        } else {
            $data['status'] = 204;
            $data['message'] = config('app_messages.FailVoucherNotFound');
            return Response::json($data, $data['status']);
        }
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
        $voucher = $this->voucher->find($id);
        if ($voucher) {
            // check if the code is already existing with the same promo date validation
            $checkVoucher = $this->voucher->where('id', '!=', $id)
                                          ->checkExistence($request->code, $request->starts_at, $request->expires_at)
                                          ->get();
                                          
            if ($checkVoucher->count() > 0) {
                $data['message'] = config('app_messages.FailVoucherAlreadyExist');
                $data['status'] = 409;
            } else {
                $voucher = $voucher->fill($request->toArray());
                $data['data'] = new VoucherResource($voucher);
                $data['message'] = config('app_messages.SuccessVoucherUpdated');
                $data['status'] = 200;
            }
            return Response::json($data, $data['status']);
        } else {
            $data['status'] = 204;
            $data['message'] = config('app_messages.FailVoucherNotFound');
            return Response::json($data, $data['status']);
        }
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        $checkExistence = $this->voucher->find($id);

        if ($checkExistence) {
            $checkExistence->delete();
            $data['message'] = config('app_messages.SuccessVoucherDestroyed');
            $data['status'] = 200;
            return Response::json($data, $data['status']);
        } else {
            $data['message'] = config('app_messages.FailVoucherNotFound');
            $data['status'] = 204;
            return Response::json($data, $data['status']);
        }
    }

    /**
    * used in validating promo code while typing on field.
    *
    * @return \Illuminate\Http\Response
    */
    public function validateCode(Request $request)
    {
        // return $request->all();
        $user = $request->user('api');
        $userId = $user ? $user->id : $request->user_id;

        //check if exist
        $voucher = $this->voucher->where('code', $request->code)->first();

        $validate = $this->validateVoucherCode($request->code, $userId);
        if ($validate['status']) {
            if ($voucher->max_uses) {
                //check if valid for specific mimimum amount
                if ($request->total_amount >= $voucher->min_amt_availability) {
                    //update max users user
                    // $voucher->decrement('max_uses');

                    //check in voucher user if user already avail this promo and check its max uses per user
                    $checkUserVoucherUsage  = $this->voucherUser->where('voucher_id', $voucher->id)
                                                                ->where('user_id', $userId)
                                                                ->first();
                    if ($checkUserVoucherUsage) {
                        if ($voucher->max_uses_user != $checkUserVoucherUsage->uses ||
                            $voucher->max_uses_user > $checkUserVoucherUsage->uses) {
                            $isFixed = $voucher->is_fixed ==1 ? false : true;
                            //compute discount from helpers
                            $discount = computeDiscount($request->total_amount, $voucher->discount_amount, $isFixed);

                           //save used voucher in user
                            //check amount of discount must not exceed the maximum amount capacity of
                            //a voucher code
                            $discountData['original_price'] = number_format($discount['original_price'], 2);
                            if ($discount['discount'] > $voucher->max_amt_cap) {
                                $discount = $voucher->max_amt_cap;
                                $discountData['total'] = number_format($request->total_amount - $discount, 2);
                                $discountData['discount'] = number_format($discount, 2);
                                $data['message'] = 'Maximum amount capacity is '.$voucher->max_amt_cap;
                            } else {
                                $discountData['total'] = $discount['total'];
                                $discountData['discount'] = number_format($discount['discount'], 2);
                            }
                            //save used voucher in user
                            $data['voucher_id'] = $voucher->id;
                            
                            $data['data'] = $discountData;
                            $statusCode = 200;
                            //end check amount of discount
                        } else {
                            $data['message'] = config('app_messages.FailVoucherLimitedPerCustomer');
                            $statusCode = 400;
                        }
                    } else {
                        //not yet in voucher user
                        $isFixed = $voucher->is_fixed ==1 ? false : true;
                        //compute discount from helpers
                        $discount = computeDiscount($request->total_amount, $voucher->discount_amount, $isFixed);
                        //save used voucher in user
                        //check amount of discount must not exceed the maximum amount capacity of
                        //a voucher code
                        $discountData['original_price'] = number_format($discount['original_price'], 2);
                        if ($discount['discount'] > $voucher->max_amt_cap) {
                            $discount = $voucher->max_amt_cap;
                            $discountData['total'] = number_format($request->total_amount - $discount, 2);
                            $discountData['discount'] = number_format($discount, 2);
                            $data['message'] = 'Maximum amount capacity is '.$voucher->max_amt_cap;
                        } else {
                            $discountData['total'] = $discount['total'];
                            $discountData['discount'] = number_format($discount['discount'], 2);
                        }
                        //save used voucher in user
                        $data['voucher_id'] = $voucher->id;
                        
                        $data['data'] = $discountData;
                        $statusCode = 200;
                        //end check amount of discount
                    }
                    //end check in voucher user if user already avail this promo and check its max uses per user
                } else {
                    $data['message'] = config('app_messages.PurchaseAmt').$voucher->min_amt_availability.' and above.';
                    $statusCode = 400;
                }
            } else {
                $data['message'] = config('app_messages.FailVoucherLimited');
                $statusCode = 400;
            }
        } else {
            $data['message'] = $validate['message'];
            $statusCode = 400;
        }
        return response()->json($data, $statusCode);
    }

    public function getVoucherDiscount(Request $request)
    {
        $user = $request->user('api');
        $userId = $user ? $user->id : $request->user_id;
        // return $request->all();
        //check if exist
        $voucher = $this->voucher->where('code', $request->code)->first();
        
        if ($voucher) {
            //validate voucher codes
            $validate = $this->validateVoucherCode($request['code'], $userId);
            if ($validate['status']) {
                if ($voucher->max_uses) {
                    //check if valid for specific mimimum amount
                    if ($request->total_amount >= $voucher->min_amt_availability) {
                        //update max users user
                        // $voucher->decrement('max_uses');
                        //check in voucher user if user already avail this promo and check its max uses per user
                        $checkUserVoucherUsage  = $this->voucherUser->where('voucher_id', $voucher->id)
                                                                    ->where('user_id', $userId)
                                                                    ->first();
                        if ($checkUserVoucherUsage) {
                            if ($voucher->max_uses_user != $checkUserVoucherUsage->uses ||
                                $voucher->max_uses_user > $checkUserVoucherUsage->uses) {
                                $isFixed = $voucher->is_fixed ==1 ? false : true;
                                //compute discount from helpers
                                $discount =computeDiscount($request->total_amount, $voucher->discount_amount, $isFixed);

                                //save used voucher in user
                                //check amount of discount must not exceed the maximum amount capacity of
                                //a voucher code
                                $discountData['original_price'] = number_format($discount['original_price'], 2);
                                if ($discount['discount'] > $voucher->max_amt_cap) {
                                    $discount = $voucher->max_amt_cap;
                                    $discountData['total'] = number_format($request->total_amount - $discount, 2);
                                    $discountData['discount'] = number_format($discount, 2);
                                    $data['message'] = 'Maximum amount capacity is '.$voucher->max_amt_cap;
                                } else {
                                    $discountData['total'] = $discount['total'];
                                    $discountData['discount'] = number_format($discount['discount'], 2);
                                }
                                //save used voucher in user
                                $data['voucher_id'] = $voucher->id;
                                
                                $data['data'] = $discountData;
                                $statusCode = 200;
                                //end check amount of discount
                            } else {
                                $data['message'] = config('app_messages.FailVoucherLimitedPerCustomer');
                                $statusCode = 400;
                            }
                        } else {
                            //not yet in voucher user
                            $isFixed = $voucher->is_fixed ==1 ? false : true;
                            //compute discount from helpers
                            $discount = computeDiscount($request->total_amount, $voucher->discount_amount, $isFixed);
                            //save used voucher in user
                            //check amount of discount must not exceed the maximum amount capacity of
                            //a voucher code
                            $discountData['original_price'] = number_format($discount['original_price'], 2);
                            if ($discount['discount'] > $voucher->max_amt_cap) {
                                $discount = $voucher->max_amt_cap;
                                $discountData['total'] = number_format($request->total_amount - $discount, 2);
                                $discountData['discount'] = number_format($discount, 2);
                                $data['message'] = 'Maximum amount capacity is '.$voucher->max_amt_cap;
                            } else {
                                $discountData['total'] = $discount['total'];
                                $discountData['discount'] = number_format($discount['discount'], 2);
                            }
                            //save used voucher in user
                            $data['voucher_id'] = $voucher->id;
                            $data['data'] = $discountData;
                            $statusCode = 200;
                            //end check amount of discount
                        }
                        //end check in voucher user if user already avail this promo and check its max uses per user
                    } else {
                        $msg = config('app_messages.PurchaseAmt').$voucher->min_amt_availability.' and above.';
                        $data['message'] = $msg;
                        $statusCode = 400;
                    }
                } else {
                    $data['message'] = config('app_messages.FailVoucherLimited');
                    $statusCode = 400;
                }
            } else {
                $data['message'] = $validate['message'];
                $statusCode = 400;
            }
        } else {
            $statusCode = 400;
            $data['message'] = config('app_messages.FailVoucherNotFound');
        }
        return response()->json($data, $statusCode);
    }

    // test controller
    public function testcontrol(Request $request)
    {
        return $this->promoDiscount('10%OFF', 1);
    }
}
