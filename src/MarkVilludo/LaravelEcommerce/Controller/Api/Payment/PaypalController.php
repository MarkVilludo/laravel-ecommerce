<?php

namespace App\Http\Controllers\Api\Payment;

use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use URL;
use Session;
use Redirect;

//-------------------------
//All Paypal Details class
//-------------------------

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;

//transactions
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Models\PaymentTransaction;
use App\Models\OngoingCheckout;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\CustomerAddress;
use App\Models\ProductVariant;
use App\Traits\VoucherTrait;
use App\Models\VoucherUsers;
use App\Models\OrderNote;
use App\Models\Voucher;
use App\Mail\OrderMail;
use App\Models\Product;
use App\Helpers\Helper;
use App\Models\Order;
use App\User;
use DB;

class PaypalController extends Controller
{
    private $api_context;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        CartItem $cartItem,
        Order $order,
        OrderItem $orderItem,
        Product $product,
        productVariant $productVariant,
        CustomerAddress $billingAddress,
        CustomerAddress $shippingAddress,
        User $user,
        Voucher $voucher,
        VoucherUsers $voucherUser,
        PaymentTransaction $paymentTrasaction,
        OngoingCheckout $ongoingCheckout,
        OrderNote $orderNote
    ) {
        $this->cartItem = $cartItem;
        $this->order = $order;
        $this->orderItem = $orderItem;
        $this->product = $product;
        $this->billingAddress = $billingAddress;
        $this->shippingAddress = $shippingAddress;
        $this->user = $user;
        $this->voucherUser = $voucherUser;
        $this->voucher = $voucher;
        $this->productVariant = $productVariant;
        $this->paymentTrasaction = $paymentTrasaction;
        $this->ongoingCheckout = $ongoingCheckout;
        $this->orderNote = $orderNote;

        /** PayPal api context **/
        $paypal_conf = \Config::get('paypal');
        $this->api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret']
        ));
        $this->api_context->setConfig($paypal_conf['settings']);
    }
    public function index()
    {
        return view('paypal.paywithpaypal');
    }
    public function payWithpaypal(Request $request)
    {

        // return $request->all();
        //Start db transactions
        //Begin database transaction
        DB::beginTransaction();
        $userId = $request->user_id;
        $shoppingBags = $this->cartItem->where('user_id', $userId)->get();

        /** Add order number to session **/

        $orderNum = date('Ymdhis').''. str_pad($userId, 1, rand(2, 99), STR_PAD_LEFT).''.$userId;
       

        if (count($shoppingBags) > 0) {
            $totalAmount = [];
            $grandTotalAmount = [];
            // $request->cart;

            $failedCount = 0;
            foreach ($shoppingBags as $key => $cart) {
                //get product details
                $product = $this->product->find($cart['product_id']);


                $totalAmount[] = ($cart['quantity'] * $product['selling_price']);
                $grandTotalAmount[] = ($cart['quantity'] * $product['selling_price']);

                //for discount get amount by regular price less selling price of product
                $discountAmount  = $product['regular_price'] - $product['selling_price'];
                //then update order discount details
                $discountPercentage = 0;
                //get discount percentage
                if ($product['regular_price'] != $product['selling_price']) {
                    $discountPercentage = round(($discountAmount / $product['regular_price']) * 100, 3);
                }
            }

            //Checking if all needed data is properly saved in db or else remove
            //all saved data and return it's error message(s)
            if ($failedCount > 0) {
                DB::rollBack();
                $statusCode = 400;
                return Redirect::to(url('/toppicks#/bags/failed'));
            } else {
                DB::commit();

                /** Add order id to session **/
                $voucherDiscount = $request->discount ? $request->discount: 0;
                Session::put('voucher_id', $request->voucher_id);
                Session::put('voucher_discount', $voucherDiscount);
                Session::put('orderNumber', $orderNum);
                Session::put('userId', $userId);

                
                //Paypal
                $payer = new Payer();
                $payer->setPaymentMethod('paypal');
                $item_1 = new Item();


                $item_1->setName('Fashion 21 Ecommerce') /** item name **/
                    ->setDescription('Order number: '.$orderNum) //order number
                    ->setCurrency('PHP')
                    ->setQuantity(1)
                    ->setPrice(array_sum($totalAmount) - $voucherDiscount); /** unit price **/

                $item_list = new ItemList();
                $item_list->setItems(array($item_1));

                $amount = new Amount();
                $amount->setCurrency('PHP')
                    ->setTotal(array_sum($totalAmount) - $voucherDiscount);

                $transaction = new Transaction();
                $transaction->setAmount($amount)
                    ->setItemList($item_list)
                    ->setDescription('Transaction Details'); //Label

                $redirect_urls = new RedirectUrls();
                $redirect_urls->setReturnUrl(URL::to('payment/paypal/status')) /** Specify return URL **/
                    ->setCancelUrl(URL::to('payment/paypal/status'));
                $payment = new Payment();
                $payment->setIntent('Sale')
                    ->setPayer($payer)
                    ->setRedirectUrls($redirect_urls)
                    ->setTransactions(array($transaction));
                /** dd($payment->create($this->api_context));exit; **/
                try {
                    $payment->create($this->api_context);
                } catch (\PayPal\Exception\PPConnectionException $ex) {
                    if (\Config::get('app.debug')) {
                        \Session::put('error', 'Connection timeout');
                        return Redirect::to('/');
                    } else {
                        \Session::put('error', 'Some error occur, sorry for inconvenient');
                        return Redirect::to('/');
                    }
                }
                foreach ($payment->getLinks() as $link) {
                    if ($link->getRel() == 'approval_url') {
                        $redirect_url = $link->getHref();
                        break;
                    }
                }
                // return $redirect_url;
                $paymentAmount = array_sum($totalAmount) - $voucherDiscount;
                if ($paymentAmount > 500000) {
                    $statusCode = 400;
                    return Redirect::to(url('/toppicks#/bags/overpayment'));
                }
                /** add payment ID to session **/
                Session::put('paypal_payment_id', $payment->getId());
                if (isset($redirect_url)) {
                    /** redirect to paypal **/
                    return Redirect::away($redirect_url);
                }
                \Session::put('error', 'Unknown error occurred');
                return Redirect::to('/');
            }
        } else {
            $data['message'] = config('app_messages.CannotBeEmptyShoppingBag');
            $statusCode = 400;
        }

        return response()->json($data, $statusCode);
    }
    public function getPaymentStatus(Request $request)
    {
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('paypal_payment_id');
        /** clear the session payment ID **/
        Session::forget('paypal_payment_id');
        if (empty(Input::get('PayerID')) || empty(Input::get('token'))) {
            \Session::put('error', 'Payment failed');
            return Redirect::to('/');
        }
        $payment = Payment::get($payment_id, $this->api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId(Input::get('PayerID'));
        /**Execute the payment **/
        $result = $payment->execute($execution, $this->api_context);
        if ($result->getState() == 'approved') {
            //----------------
            // Here Write your database logic like that insert record or value in database if you want
            //----------------
            //
            //Move here code for saving orders, transaction, less variant inventory, voucher user, and etc.
            // then return exact response needed of the system.
            //Start db transactions
            //Begin database transaction
            DB::beginTransaction();
            $userId = Session::get('userId');

            $shoppingBags = $this->cartItem->where('user_id', $userId)->get();

            $failedCount = 0;

            $totalAmount = [];
            $grandTotalAmount = [       ];
            if (count($shoppingBags) > 0) {
                $order = new $this->order;
                $order->user_id = $userId;
                $order->remarks = 'Paid through Paypal';

                // Processing order
                $order->status_id = config('setting.ProcessingOrderStatus');
                $order->number = Session::get('orderNumber');

                //get default billing of log user
                $billingAddress = $this->billingAddress->where('user_id', $userId)
                                                       ->where('default_billing', 1)
                                                       ->first();
                                                       
                $shippingAddress = $this->shippingAddress->where('user_id', $userId)
                                                       ->where('default_shipping', 1)
                                                       ->first();

                if (!$billingAddress) {
                    //rollback anything if failed to save any of product faqs
                    $failedCount += 1;
                    $data['message'] = 'Please provide billing address.';
                } elseif (!$shippingAddress) {
                    $failedCount += 1;
                    $data['message'] = 'Please provide shipping address.';
                } else {
                    //Check what address will going to used
                    $order->customer_billing_address_id = $billingAddress->id;
                    $order->customer_shipping_address_id = $shippingAddress->id;
                }

                     /** Get the payment ID before session clear **/
                $voucherDiscount = Session::get('voucher_discount');
                $order->shipping_fee = $request->shipping_fee ? $request->shipping_fee : 0;
                $order->voucher_discount = $voucherDiscount;


                if ($order->save()) {
                    $orderId = $order->id;
                } else {
                    $failedCount += 1;
                }

                $failedCount = 0;
                foreach ($shoppingBags as $key => $cart) {
                    //get product details
                    $product = $this->product->find($cart['product_id']);

                    $newOrderItem = new $this->orderItem;
                    $newOrderItem->product_id = $cart['product_id'];
                    $newOrderItem->variant_id = $cart['variant_id'];
                    $newOrderItem->item = $product['name'];
                    $newOrderItem->order_id = $orderId;

                    $newOrderItem->selling_price = $product['selling_price'];
                    $newOrderItem->regular_price = $product['regular_price'];
                    $newOrderItem->total = ($cart['quantity'] * $product['selling_price']);

                    $totalAmount[] = ($cart['quantity'] * $product['regular_price']);
                    $grandTotalAmount[] = ($cart['quantity'] * $product['selling_price']);

                    //for discount get amount by regular price less selling price of product
                    $discountAmount  = $product['regular_price'] - $product['selling_price'];
                    //then update order discount details
                    $discountPercentage = 0;
                    //get discount percentage
                    if ($product['regular_price'] != $product['selling_price']) {
                        $discountPercentage = round(($discountAmount / $product['regular_price']) * 100, 3);
                    }

                    $newOrderItem->quantity = $cart['quantity'];
                    $newOrderItem->discount = $discountPercentage;
                    $newOrderItem->save();


                    //save order notes for received orders
                    $newTransaction = new $this->orderNote;
                    $newTransaction->order_id = $orderId;
                    $newTransaction->order_item_id = $newOrderItem->id;
                    $newTransaction->notes = config('app_messages.ThankYouForShopping');
                    $newTransaction->save();
                    //end

                    //Update stocks from variant
                    $variant = $this->productVariant->where('id', $cart['variant_id'])
                                                    ->where('product_id', $cart['product_id'])
                                                    ->first();

                    if ($variant) {
                        //Less number of item in product variants
                        //based on selected product (s)
                        if ($cart['variant_id']) {
                            //based on selected product (s)
                            // $variant->decrement('inventory', $cart['quantity']);
                            //automatically decrement in validate orders in ongoing.
                            //remove data on ongoing
                            $this->ongoingCheckout->where('user_id', $userId)
                                                  ->where('variant_id', $cart['variant_id'])
                                                  ->where('product_id', $cart['product_id'])
                                                  ->delete();
                        }
                    }
                    // remove items in cart that save in order items (package or product)
                    $cart = $this->cartItem->find($cart['id']);
                    if ($cart) {
                        $cart->delete();
                    }
                }

                 $updateOrder = $this->order->find($orderId);
                //Update saved order
                //subtotal from regular price
                $order->sub_total = array_sum($totalAmount);
                //less discount in total amount if any
                $updateOrder->total_amount = array_sum($grandTotalAmount);
                //save updateOrder details and voucher details in specific user
                $updateOrder->grand_total = array_sum($grandTotalAmount) - $voucherDiscount;
                $updateOrder->balance = 0;
                
                $updateOrder->update();

                //save order details and voucher details in specific user
                $voucherId = Session::get('voucher_id');

                if ($voucherId) {
                     //update used voucher
                    $voucher = $this->voucher->find($voucherId);
                    $voucher->increment('uses');
                    $voucher->decrement('max_uses');

                       //check if this voucher is already used and count number of used but
                        //limited on max uses per user in voucher
                    $checkVoucherUser = $this->voucherUser->where('voucher_id', $voucherId)
                                                          ->where('user_id', $userId)
                                                          ->first();

                    if ($checkVoucherUser) {
                        // if ($voucher->max_uses_user > $checkVoucherUser) {
                            $checkVoucherUser->increment('uses');
                        // }
                    } else {
                        $newVoucherUser = new $this->voucherUser;
                        $newVoucherUser->order_id = $orderId;
                        $newVoucherUser->user_id = $userId;
                        $newVoucherUser->voucher_id = $voucherId;
                        $newVoucherUser->date_used = date('Y-m-d h:i:s');
                        $newVoucherUser->uses = 1;
                        $newVoucherUser->save();
                    }
                }

                //Checking if all needed data is properly saved in db or else remove
                //all saved data and return it's error message(s)
                if ($failedCount > 0) {
                    DB::rollBack();

                    //remove order saved details
                    $removeOrder = $this->order->where('id', $orderId)->delete();

                    return Redirect::to(url('/toppicks#/bags/failed'));

                    $statusCode = 400;
                } else {
                    //Save transaction details
                    $paymentTrasaction = new $this->paymentTrasaction;
                    $paymentTrasaction->user_id = $userId;
                    $paymentTrasaction->order_id = $orderId;
                    $paymentTrasaction->notes = $result->id;
                    $paymentTrasaction->type = 'Pay through Paypal (Web)';
                    $paymentTrasaction->payment_method = 'Paypal';
                    $paymentTrasaction->amount = array_sum($grandTotalAmount) - $voucherDiscount;
                    $paymentTrasaction->save();
                    //end save transactions

                    DB::commit();

                    //check if has cache cart items
                    if (Cache::has('cartItems'.$userId)) {
                        //clear cache products
                        Cache::forget('cartItems'.$userId);
                        //end clear cache
                    }

                     /** clear the session order number **/
                    Session::forget('orderNumber');

                    $data['message'] = config('app_messages.SuccessSaveOrdersInCart');
                    $statusCode = 200;

                    // Send mail
                    //Get order details
                    $order = $this->order->where('id', $orderId)
                                    ->with('user.addresses')
                                    ->with('notes')->with('orderItems.product')
                                    ->with('orderItems.productImage')
                                    ->first();

                    Mail::to($order->user->email)->send(new OrderMail($order));

                    //Notify admin users for new order
                    Helper::makeNotificationsToAdmin($orderId, 'New Order', 'Received new order.');
                    //end notify
                }
            }
            $data['message'] = config('app_messages.SuccessPlacedOrder');
            $data['response'] = $result;

            return Redirect::to(url('/toppicks#/bags/success'))
                            ->with('message', config('app_messages.SuccessPlacedOrder'));
            // return redirect()->route('api.payment_paypal')->with($data);
        }

        return redirect('/toppicks#/bags')->with('message', 'Payment failed');
    }
}
