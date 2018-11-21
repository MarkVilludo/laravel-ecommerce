<?php

namespace App\Http\Controllers\Api\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Braintree_Transaction;
use App\Models\PaymentTransaction;
use App\Models\Order;

class BraintreeController extends Controller
{

    public function __construct(Order $order, PaymentTransaction $paymenTransaction)
    {
        $this->order = $order;
        $this->paymenTransaction = $paymenTransaction;
    }
    public function postPayment(Request $request)
    {
        // return $request->all();
        // $payload = $request->input('payload', false);
        // $nonce = $payload['nonce'];

        $status = Braintree_Transaction::sale([
            'amount' => $request->amount,
            'customerId' => '882374366',
            'paymentMethodNonce' => 'nonce-from-the-client',
            'options' => [
                'submitForSettlement' => true
            ]
        ]);
        if ($status->success) {
            // return $request->order_id;
            $order = $this->order->find($request->order_id);

            // Save payment method and amount in payment transaction table
            if ($order->balance > 0 && $paymentAmount) {
                $newPaymentTransaction = new $this->paymenTransaction;
                $newPaymentTransaction->order_id = $request->order_id;
                $newPaymentTransaction->payment_method = 1; // paypal initial
                $newPaymentTransaction->amount = $paymentAmount;
                $newPaymentTransaction->updated_by = auth()->user ? auth()->user->id : null;
                $newPaymentTransaction->save();

                //Save Check order status if paid or not
                // $order = $this->order->find($orderId);
                $order->grand_total = $order->grand_total - $paymentAmount;
                $order->balance = $order->balance - $paymentAmount;
                // $order->status_id = 1; //initial status id for paid
                $order->save();

                //Mark as paid when balance is less than equals to 0/zero
                if ($order->balance <= 0.00) {
                    $order->increment('status');
                }
            }
            return response()->json($status);
        } else {
            return response()->json(['message' => 'Something went wrong.']);
        }
    }
}
