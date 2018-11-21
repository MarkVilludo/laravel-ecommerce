<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Address\AddressStoreRequest;
use App\Http\Resources\CustomerAddressResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;

class CustomerAddressController extends Controller
{

   
    //construct model variable
    public function __construct(CustomerAddress $customerAddress)
    {
        $this->customerAddress = $customerAddress;
    }
    /**
     * Get all billing address per customer
     *
     * @return \Illuminate\Http\Response
    */
    public function index($userId)
    {
        //Cache data
        if (Cache::has('customerAddress'.$userId)) {
            $customerAddress = Cache::get('customerAddress'.$userId);
        } else {
            $customerAddress = Cache::remember(
                'customerAddress'.$userId,
                config('cache.cacheTime'),
                function () use ($userId) {
                    return  $this->customerAddress->where('user_id', $userId)
                                                  ->orderBy('default_billing', 'desc')
                                                  ->orderBy('default_shipping', 'desc')
                                                  ->get();
                }
            );
        }
        //end cache product details

        if ($customerAddress) {
            $data = CustomerAddressResource::collection($customerAddress);
            return $data;
        } else {
            $data['message'] = config('app_messages.ThereIsNoAddressAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }
    /**
     * Store a newly billing address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddressStoreRequest $request, $userId)
    {
        // return $request->all();
        $checkIfHasAddress = $this->customerAddress->where('user_id', $userId)->get();

        //count data
        $countAddress = count($checkIfHasAddress);

        $newAddress = new $this->customerAddress;
        $newAddress->user_id = $userId;
        $newAddress->first_name = $request->first_name  ? $request->first_name : '';
        $newAddress->last_name = $request->last_name  ? $request->last_name : '';
        $newAddress->complete_address = $request->complete_address;
        $newAddress->province_id = $request->province_id;
        $newAddress->city_id = $request->city_id;
        $newAddress->country_id = $request->country_id;
        $newAddress->barangay = $request->barangay;
        $newAddress->mobile_number = $request->mobile_number;
        $newAddress->zip_code = $request->zip_code;
        $newAddress->default_billing = $countAddress ? 0 : 1;
        $newAddress->default_shipping = $countAddress ? 0 : 1;

        if ($newAddress->save()) {
            if (Cache::has('customerAddress'.$userId)) {
                //clear cache products
                Cache::forget('customerAddress'.$userId);
                //end clear cache
            }
            $data['message'] = config('app_messages.SuccessAddedAddress');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.SomethingWentWrong');
            $statusCode = 400;
        }
        return response()->json(['data' => $data], $statusCode);
    }

    /**
     * Update the specified billing address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AddressStoreRequest $request, $userId, $addressId)
    {
        // return $addressId;
        $address = $this->customerAddress->where('user_id', $userId)->where('id', $addressId)->first();

        if ($address) {
            $completeAddress = $request->complete_address ? $request->complete_address : $address->complete_address;
            $defBilling = $request->default_billing ? $request->default_billing : $address->default_billing;

            $address->user_id = $userId;
            $address->first_name = $request->first_name ? $request->first_name : $address->first_name;
            $address->last_name = $request->last_name ? $request->last_name : $address->last_name;
            $address->complete_address = $completeAddress;
            $address->province_id = $request->province_id ? $request->province_id : $address->province_id;
            $address->city_id = $request->city_id ? $request->city_id : $address->city_id;
            $address->country_id = $request->country_id ? $request->country_id : $address->country_id;
            $address->barangay = $request->barangay ? $request->barangay : $address->barangay;
            $address->mobile_number = $request->mobile_number ? $request->mobile_number : $address->mobile_number;
            $address->zip_code = $request->zip_code ? $request->zip_code : $address->zip_code;
            $address->default_billing = $defBilling;

            if ($address->update()) {
                if (Cache::has('customerAddress'.$userId)) {
                    //clear cache products
                    Cache::forget('customerAddress'.$userId);
                    //end clear cache
                }
                $data['message'] = config('app_messages.SuccessUpdatedBillingAddress');
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.SomethingWentWrong');
                $statusCode = 400;
            }
        } else {
            $data['message'] = config('app_messages.AddressNotfound');
            $statusCode = 404;
        }
        return response()->json(['data' => $data], $statusCode);
    }

    public function setDefaultAddress(Request $request, $userId, $addressId)
    {

        //check type
        $type = $request->type;
        // return $addressId;
        //update default billing address
        //check if user has billing address
        $customerAddress = $this->customerAddress->where('user_id', $userId)->get();
        if (count($customerAddress) > 0) {
            $address = $this->customerAddress->where('user_id', $userId)->where('id', $addressId)->first();

            //check if billing or shipping address
            //check if with existing detault address then toggle to not detault except selected new one
            $this->customerAddress->checkIfHasDefaultAddress($userId, $addressId, $type);

            if ($address) {
                //if no request default set selected address as default.
                if ($type == 'billing') {
                    $address->default_billing = 1;
                } else {
                    $address->default_shipping = 1;
                }

                $address->update();
                if (Cache::has('customerAddress'.$userId)) {
                    //clear cache products
                    Cache::forget('customerAddress'.$userId);
                    //end clear cache
                }

                $statusCode = 200;
                $data['message'] = config('app_messages.SuccessSetDefaultBillingAddress');
            } else {
                $statusCode = 404;
                $data['message'] = config('app_messages.AddressNotfound');
            }
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.NoAddressFoundOnUser');
        }
        return response()->json(['data' => $data], $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId, $customerAddressId)
    {
        $customerAddress = $this->customerAddress->where('id', $customerAddressId)
                                                 ->where('user_id', $userId)->first();

        if ($customerAddress) {
            $customerAddress->delete();

            //check if address is only one left and therefor set as default address
            //for shipping and billing address
            $remainingAddress = $this->customerAddress->where('user_id', $userId)->get();

            if (count($remainingAddress) == 1) {
                $updateBillingShippingAddress = $this->customerAddress->where('user_id', $userId)
                                                                      ->update(['default_billing' => 1,
                                                                                'default_shipping' => 1]);
            }

            if (count($remainingAddress) > 1) {
                $checkifHasExistingDefaultAddress =  $this->customerAddress->where('user_id', $userId)
                                                                            ->where('default_billing', 1)
                                                                            ->first();

                $checkifHasExistingShippingAddress =  $this->customerAddress->where('user_id', $userId)
                                                                            ->where('default_shipping', 1)
                                                                            ->first();
                
                $updateBillingShippingAddress = $this->customerAddress->where('user_id', $userId)
                                                                      ->latest()->first();
                if (!$checkifHasExistingDefaultAddress) {
                    $updateBillingShippingAddress->default_billing = 1;
                }
                if (!$checkifHasExistingShippingAddress) {
                    $updateBillingShippingAddress->default_shipping = 1;
                }
                $updateBillingShippingAddress->update();
            }

            if (Cache::has('customerAddress'.$userId)) {
                //clear cache products
                Cache::forget('customerAddress'.$userId);
                //end clear cache
            }


            $data['message'] = config('app_messages.SuccessDeletedCustomerAddress');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.AddressNotfound');
            $statusCode = 404;
        }

        return response()->json(['data' => $data], $statusCode);
    }
}
