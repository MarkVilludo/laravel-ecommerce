<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Braintree_Transaction;

class BraintreeController extends Controller
{

    public function __construct()
    {
    }
    public function paymentView()
    {

        return view('braintree.payment');
    }
    public function process(Request $request)
    {
        // return $request->all();
        $payload = $request->input('payload', false);
        $nonce = $payload['nonce'];

        $status = Braintree_Transaction::sale([
            'amount' => '10.00',
            'customerId' => '882374366',
            'paymentMethodNonce' => 'nonce-from-the-client',
            'options' => [
                'submitForSettlement' => true
            ]
        ]);
        return response()->json($status);
    }
}
