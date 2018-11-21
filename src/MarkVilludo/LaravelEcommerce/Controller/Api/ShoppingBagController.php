<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Bags\StoreProductInBagsRequest;
use App\Http\Resources\ProductVariantResource;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\PaymentTransaction;
use App\Models\CustomerAddress;
use App\Models\OngoingCheckout;
use App\Models\ProductVariant;
use App\Traits\VoucherTrait;
use App\Models\VoucherUsers;
use Illuminate\Http\Request;
use App\Models\PackageItem;
use App\Models\OrderItem;
use App\Models\OrderNote;
use App\Models\CartItem;
use App\Models\Wishlist;
use App\Mail\OrderMail;
use App\Models\Voucher;
use App\Models\Package;
use App\Models\Product;
use App\Helpers\Helper;
use App\Models\Order;
use App\User;
use Validator;
use Config;
use DB;

class ShoppingBagController extends Controller
{
    use VoucherTrait;

    //construct model variable
    public function __construct(
        CartItem $cartItem,
        Order $order,
        OrderItem $orderItem,
        Product $product,
        CustomerAddress $billingAddress,
        CustomerAddress $shippingAddress,
        User $user,
        ProductVariant $productVariant,
        Package $package,
        PackageItem $packageItem,
        Wishlist $wishList,
        VoucherUsers $voucherUser,
        Voucher $voucher,
        PaymentTransaction $paymentTrasaction,
        OrderNote $orderNote,
        OngoingCheckout $ongoingCheckout
    ) {

        $this->productVariant = $productVariant;
        $this->cartItem = $cartItem;
        $this->order = $order;
        $this->orderItem = $orderItem;
        $this->product = $product;
        $this->billingAddress = $billingAddress;
        $this->shippingAddress = $shippingAddress;
        $this->user = $user;
        $this->package = $package;
        $this->packageItem = $packageItem;
        $this->wishList = $wishList;
        $this->voucherUser = $voucherUser;
        $this->voucher = $voucher;
        $this->paymentTrasaction = $paymentTrasaction;
        $this->orderNote = $orderNote;
        $this->ongoingCheckout = $ongoingCheckout;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $userId)
    {
        // return $userId;
        // Cache::forget('cartItems'.$userId)
        $cartItems = $this->cartItem->where('user_id', $userId)->get();

        if (Cache::has('cartItems'.$userId)) {
            $cartItems = Cache::get('cartItems'.$userId);
        } else {
            $cartItems = Cache::remember(
                'cartItems'.$userId,
                config('cache.cacheTime'),
                function () use ($userId) {
                    return $this->cartItem->where('user_id', $userId)->get();
                }
            );
        }

        $subTotalAmount = [];
        foreach ($cartItems as $key => $cartItem) {
            $subTotalAmount[] = ($cartItem->quantity * $cartItem->product['selling_price']);
        }

            // return $subTotalAmount;
        $cart = CartResource::collection($cartItems);
        if ($cart) {
            $data['message'] = config('app_messages.ShowUserCartProducts');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.NoCartProducts');
            $statusCode = 404;
        }
       
        $data['sub_total'] = number_format(array_sum($subTotalAmount), 2);
        $data['products'] = $cart;

        return response()->json($data, $statusCode);
    }

    //Get live carts data
    public function getLiveShoppingBags()
    {
        //get data
        $shoppingBags = $this->cartItem->paginate(10);
        if ($shoppingBags) {
            $data['message'] = config('app_messages.ShowUserCartProducts');
            $data = CartResource::collection($shoppingBags);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoCartProducts');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }
    /**
     * add product to shopping bag
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductInBagsRequest $request, $userId)
    {
        // return $request->all();

        //check if product is exist
        $product = $this->product->find($request->product_id);

        if ($product) {
            //check if user is exist
            $checkIfExistUser = $this->user->find($userId);
            if ($checkIfExistUser) {
                $checkIfExist = $this->cartItem->where('user_id', $userId)
                                           ->where('product_id', $request->product_id)
                                           ->where('variant_id', $request->variant_id)
                                           ->first();
                if ($checkIfExist) {
                    $data['message'] = config('app_messages.ProductIsAlreadyInShoppingBags');
                    $statusCode = 200;
                } else {
                    $variant = $this->productVariant->find($request->variant_id);

                    //based on selected product (s)
                    if ($variant->inventory >= $request->quantity) {
                        // $variant->decrement('inventory', $request->quantity);

                        $newShoppingBags = new $this->cartItem;
                        $newShoppingBags->user_id = $userId;
                        $newShoppingBags->product_id = $request->product_id;
                        $newShoppingBags->quantity = $request->quantity;
                        $newShoppingBags->variant_id = $request->variant_id;
                        $newShoppingBags->discount = $request->discount  ? $request->discount : 0;

                        if ($newShoppingBags->save()) {
                            $data['message'] = config('app_messages.SuccessAddedItemInCart');
                            $statusCode = 201;
                        } else {
                            $data['message'] = config('app_messages.SomethingWentWrong');
                            $statusCode = 400;
                        }

                        if (Cache::has('cartItems'.$userId)) {
                            //clear cache products
                            Cache::forget('cartItems'.$userId);
                            //end clear cache
                        }
                    } else {
                        //rollback anything if failed to decrease inventory due out of stocks informed user
                        $productDetails = $this->product->find($request->product_id);
                        $statusCode = 400;

                        $tempVar = $productDetails->name." is with ".$variant->inventory." stocks only.";
                        $msg ="Selected variant for ".$tempVar;

                        $data['message'] = $msg;
                    }
                }
            } else {
                $data['message'] = config('app_messages.ShoppingBagCustomerNotFound');
                $statusCode = 400;
            }
        } else {
            $data['message'] = config('app_messages.ProductNotFound');
            $statusCode = 404;
        }
       
       
        return response()->json($data, $statusCode);
    }
    /**
     * add product to shopping bag in guest get product info
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getBagDetailsGuest(Request $request)
    {
        // return $request->product_id;
        $product = new ProductResource($this->product->find($request->product_id));

        if ($product) {
            $variant = new ProductVariantResource($this->productVariant->where('product_id', $request->product_id)
                                                                    ->where('id', $request->variant_id)
                                                                    ->first());

            $data['product'] = $product;
            $data['variant'] = $variant;
            $data['quantity'] = $request->quantity;

            $statusCode = 200;
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.ProductNotFound');
        }

        return response()->json($data, $statusCode);
    }

    /**
     * add product to shopping bag in guest get product info
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getBagDetailsGuestMultiple(Request $request)
    {
        // return $request->bags;

        $bagDetailsArray = [];
        $subTotalAmount = [];
        if ($request->bags) {
            $numberOfShippingDays = 5;
            $shippingDays = "Get by ".date(
                'D, d',
                strtotime(date('Y-m-d h:i:s'))
            ).' - '.date(
                'D d M Y',
                strtotime('+'.$numberOfShippingDays.' day', strtotime(date('Y-m-d h:i:s')))
            );
            
            foreach ($request->bags as $key => $bag) {
                if ($bag) {
                    $product = new ProductResource($this->product->find($bag['product_id']));
                    if ($product) {
                        $variant = new ProductVariantResource($this->productVariant
                                                                ->where('product_id', $bag['product_id'])
                                                                ->where('id', $bag['variant_id'])
                                                                ->first());


                        $subTotalAmount[] = ($bag['quantity'] * $product->selling_price);

                        $bagDetailsArray[] = [
                            'id' => $bag['id'],
                            'product' => $product,
                            'product_name' => $product->name,
                            'variant' => $variant,
                            'quantity' => $bag['quantity'],
                            'standard_shipping_days' => $shippingDays,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];

                        $statusCode = 200;
                    }
                }
            }
            $data['message'] = config('app_messages.ShowUserCartProducts');
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.NoCartProducts');
        }

        $data['sub_total'] = number_format(array_sum($subTotalAmount), 2);
        $data['products'] = $bagDetailsArray;

        return response()->json($data, $statusCode);
    }

    /**
     * Store multiple item in cart from local storage or guest users
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */

    public function storeMultipleItem(Request $request, $userId)
    {
        //Return
        // return $request->all();
        $user = $request->user('api');
        $userId = $user->id;

        if ($userId) {
            $failedCount = 0;
            $productIdsNotExist = [];
            $productVariantOutOfStocks = [];
            //check if product is exist loop bags
            foreach ($request->bags as $bag) {
                //check if product is exist
                $product = $this->product->find($bag['product_id']);
                if (!$product) {
                    // if not exist
                    $failedCount += 1;
                    $productIdsNotExist[] = $bag['product_id'];
                }
                //check if variant stocks is enough to handle this request.
                $variant = $this->productVariant->where('product_id', $bag['product_id'])
                                           ->where('id', $bag['variant_id'])
                                           ->first();

                if ($variant) {
                    $variantStocks = $variant->inventory;
                    if ($variantStocks < $bag['quantity']) {
                        // if failed
                        // $failedCount += 1;
                        $productVariantOutOfStocks[] = $variant->id;
                    }
                }
            }

            //check count of failed or product not exist.
            if ($failedCount) {
                if ($productIdsNotExist) {
                    $tempVar = ' Product ids: '.implode(",", $productIdsNotExist);
                    $data['message'] = config('app_messages.ProductNotFound').$tempVar;
                }
            } else {
                //Return message for product variant with insufficient stocks.
                if ($productVariantOutOfStocks) {
                    $data['other_datails'] = 'Out of stocks,'.' Variant ids: '.implode(",", $productVariantOutOfStocks);
                }

                //Reloop to store item(s) in bags.
                foreach ($request->bags as $bag) {
                    $checkIfExist = $this->cartItem->where('user_id', $userId)
                                           ->where('product_id', $bag['product_id'])
                                           ->where('variant_id', $bag['variant_id'])
                                           ->first();
                    //if exist product with same variant update its details
                    if ($checkIfExist) {
                        $checkIfExist->quantity = $bag['quantity'];
                        $checkIfExist->update();

                        $data['message'] = 'Successfully added and updated item in shopping bags.';
                    } else {
                        //save new item in bags
                        $variant = $this->productVariant->where('product_id', $bag['product_id'])
                                               ->where('id', $bag['variant_id'])
                                               ->first();

                        if ($variant) {
                            //based on selected product (s)
                            $variantStocks = $variant->inventory;
                            $newShoppingBags = new $this->cartItem;
                            $newShoppingBags->user_id = $userId;
                            $newShoppingBags->product_id = $bag['product_id'];
                            $newShoppingBags->quantity = $bag['quantity'];
                            $newShoppingBags->variant_id = $bag['variant_id'];
                            $newShoppingBags->discount = 0;

                            if ($newShoppingBags->save()) {
                                //check if has cache cart items
                                if (Cache::has('cartItems'.$userId)) {
                                    //clear cache products
                                    Cache::forget('cartItems'.$userId);
                                    //end clear cache
                                }
                                $data['message'] = config('app_messages.SuccessAddedItemInCart');
                            } else {
                                $data['message'] = config('app_messages.SomethingWentWrong');
                                $failedCount += 1;
                            }
                        } else {
                            $failedCount += 1;
                            $data['message'] = 'Product variant not exist for variant id: '.$bag['variant_id'];
                        }
                    }
                }
            }
        } else {
            //user not found
            $data['message'] = config('app_messages.SomethingWentWrong');
            $failedCount += 1;
        }

        if ($failedCount) {
            $statusCode = 400;
        } else {
            $statusCode = 200;
        }
        return response()->json($data, $statusCode);
    }
    /**
     * Move product from shopping bag to wishlist
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function moveItemToWishList($userId, $cartItemId)
    {
        // return $cartItemId;
        $item = $this->cartItem->where('user_id', $userId)->where('id', $cartItemId)->first();

        //check if exist
        if ($item) {
            //check if not yet exist in wishlist
            $wishList = $this->wishList->where('user_id', $userId)
                                       ->where('product_id', $item->product_id)
                                       ->where('variant_id', $item->variant_id)
                                       ->first();

            if ($wishList) {
                $statusCode = 200;
                $wishList->quantity = $item->quantity;
                $wishList->update();

                $data['message'] = 'Product is already in wishlist and successfully updated the details.';
            } else {
                $newWishList = new $this->wishList;
                $newWishList->user_id = $userId;
                $newWishList->product_id = $item->product_id;
                $newWishList->variant_id = $item->variant_id;
                $newWishList->quantity = $item->quantity;
                $newWishList->save();

                $item->delete();

                //check if has cache cart items
                if (Cache::has('cartItems'.$userId)) {
                    //clear cache products
                    Cache::forget('cartItems'.$userId);
                    //end clear cache
                }
                //check if has cache wishlist
                if (Cache::has('wishList'.$userId)) {
                    //clear cache products
                    Cache::forget('wishList'.$userId);
                    //end clear cache
                }

                $data['message'] = config('app_messages.SuccessMovedToWishlist');
            }
            $statusCode = 200;
        } else {
            $statusCode = 400;
            $data['message'] = config('app_messages.OrdertItemNotFound');
        }

        return response()->json($data, $statusCode);
    }

    /**
     * Checkout orders
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function validateOrders(Request $request)
    {
        $user = $request->user('api');
        $userId = $user->id;

        $userProductCartOrders = $this->cartItem->where('user_id', $userId)->get();

        //get all item in cart related to log user
        //create new user, customer billing address, customer shipping address if not exist email
        if (count($userProductCartOrders) > 0) {
            $failedCount = 0;

            $productWithInsufficientStocks = [];
            $productVariantToLessStocks = [];

            foreach ($userProductCartOrders as $key => $userProductCartOrder) {
                //check first the product is in stocks
                $checkProductStocks = $this->productVariant->where('product_id', $userProductCartOrder['product_id'])
                                                           ->where('id', $userProductCartOrder['variant_id'])
                                                           ->first();
                //end check exist stocks in variants
                if ($checkProductStocks->inventory >= $userProductCartOrder['quantity']) {
                    $statusCode = 200;

                    $productVariantToLessStocks[] = [
                                                    'variant_id' => $checkProductStocks->id,
                                                    'product_id' => $checkProductStocks->product_id,
                                                    'quantity' => $userProductCartOrder['quantity']
                                                ];
                } else {
                    //get product details
                    $product = $this->product->find($userProductCartOrder['product_id']);

                    $productWithInsufficientStocks[] = [
                        'product' => $product->name,
                        'remaining_stocks' => $checkProductStocks->inventory,
                        'quantity' => $userProductCartOrder['quantity']
                    ];
                    $data['products'] = $productWithInsufficientStocks;

                    $failedCount += 1;
                }
                $data['message'] = config('app_messages.ValidatedOrdersBeforeCheckout');
            }
            if ($failedCount > 0) {
                $statusCode = 400;
            } else {
                $statusCode = 200;

                 //less stocks hold for 30mins
                foreach ($productVariantToLessStocks as $key => $productVariantToLessStock) {
                    //check if exist in on going and update its details
                    $onGoing = $this->ongoingCheckout->where('product_id', $productVariantToLessStock['product_id'])
                                                    ->where('variant_id', $productVariantToLessStock['variant_id'])
                                                    ->where('user_id', $userId)
                                                    ->first();

                    //if existing in on going update its quantity
                    if ($onGoing) {
                        $onGoing->quantity = $productVariantToLessStock['quantity'];
                        $onGoing->update();
                    } else {
                        //Save new on going
                        $newOngoingData = new $this->ongoingCheckout;
                        $newOngoingData->user_id = $userId;
                        $newOngoingData->product_id = $productVariantToLessStock['product_id'];
                        $newOngoingData->variant_id = $productVariantToLessStock['variant_id'];
                        $newOngoingData->quantity = $productVariantToLessStock['quantity'];
                        $newOngoingData->save();

                        //variant
                        $variant = $this->productVariant->where('product_id', $productVariantToLessStock['product_id'])
                                                               ->where('id', $productVariantToLessStock['variant_id'])
                                                               ->first();
                        //Decrease stocks
                        $variant->decrement('inventory', $productVariantToLessStock['quantity']);
                    }
                }
            }
        } else {
            $data['message'] = config('app_messages.CannotBeEmptyShoppingBag');
            $statusCode = 400;
        }

        return response()->json($data, $statusCode);
    }
    /**
     * Checkout orders
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkout(Request $request)
    {
        // return $request->all();
        //Begin database transaction
        DB::beginTransaction();

        $user = $request->user('api');
        $userId = $user->id;

        $failedCount = 0;

        $shoppingBags = $this->cartItem->where('user_id', $userId)->get();

        //get all item in cart related to log user
        //create new user, customer billing address, customer shipping address if not exist email
        if (count($shoppingBags) > 0) {
            //check if log users only with accunt can place to orders
            // or else will create new account brfore checkout
            //Validate input
            // return $request->user;
            //store initial order

            $newOrder = new $this->order;
            $newOrder->user_id = $userId;
            $newOrder->total_amount = 0;
            $newOrder->grand_total = 0;
            // Processing order
            $newOrder->status_id = config('setting.ProcessingOrderStatus');
            $newOrder->remarks = 'Paid through Paypal';

            $orderNum = $request->order_number;

            $newOrder->number = $orderNum;

            if ($newOrder->save()) {
                $orderId = $newOrder->id;
            }

            $totalAmount = [];
            $grandTotalAmount = [];
            // $request->cart;

            if (!$shoppingBags) {
                //rollback anything if failed to save any of product faqs
                $failedCount += 1;
            }

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
                        // $variant->decrement('inventory', $cart['quantity']);
                        //automatically decrement in validate orders in ongoing.
                        //remove data on ongoing
                        $this->ongoingCheckout->where('user_id', $userId)
                                              ->where('variant_id', $cart['variant_id'])
                                              ->where('product_id', $cart['product_id'])
                                              ->delete();
                                              
                    //     //rollback anything if failed to decrease inventory due out of stocks informed user
                    //     $productDetails = $this->product->find($cart['product_id']);
                    //     $failedCount += 1;
                    //     $data['message'] = "Apologies, we're almost out of stock.
                    //     Please select fewer quantify for".' '.$productDetails->name;
                }
                // remove items in cart that save in order items (package or product)
                $cart = $this->cartItem->find($cart['id']);
                if ($cart) {
                    $cart->delete();
                }
            }

            //Update saved order
            $voucherDiscount = $request->discount ? $request->discount: 0;
            $order = $this->order->find($orderId);
            $order->shipping_fee = $request->shipping_fee ? $request->shipping_fee : 0;
            $order->voucher_discount = $voucherDiscount;

            //get default billing of log user
            $billingAddress = $this->billingAddress->where('user_id', $userId)->where('default_billing', 1)->first();
            $shippingAddress = $this->shippingAddress->where('user_id', $userId)->where('default_shipping', 1)->first();

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

            //subtotal from regular price
            $order->sub_total = array_sum($totalAmount);
            //less discount in total amount if any
            $order->total_amount = array_sum($grandTotalAmount);
            //save order details and voucher details in specific user
            if ($request->voucher_id) {
                //update used voucher
                $voucher = $this->voucher->find($request->voucher_id);
                $voucher->increment('uses');
                $voucher->decrement('max_uses');

                //check if this voucher is already used and count number of used but
                //limited on max uses per user in voucher

                $checkVoucherUser = $this->voucherUser->where('voucher_id', $request->voucher_id)
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
                    $newVoucherUser->voucher_id = $request->voucher_id;
                    $newVoucherUser->date_used = date('Y-m-d h:i:s');
                    $newVoucherUser->uses = 1;
                    $newVoucherUser->save();
                }
            }

            $order->grand_total = array_sum($grandTotalAmount) - $voucherDiscount;
            $order->balance = 0;
            $order->save();

            if ($request->paypal_transaction_id) {
                //Save transaction details
                $paymentTrasaction = new $this->paymentTrasaction;
                $paymentTrasaction->user_id = $userId;
                $paymentTrasaction->order_id = $orderId;
                $paymentTrasaction->notes = $request->paypal_transaction_id;
                $paymentTrasaction->type = 'Pay through Paypal (Mobile)';
                $paymentTrasaction->payment_method = 'Paypal';
                $paymentTrasaction->amount = array_sum($grandTotalAmount) - $voucherDiscount;
                $paymentTrasaction->save();
                //end save transactions
            } else {
                $failedCount += 1;
                $data['message'] = 'Please provide paypal transaction id.';
            }

            //Checking if all needed data is properly saved in db or else remove
            //all saved data and return it's error message(s)
            if ($failedCount > 0) {
                DB::rollBack();
                $statusCode = 400;
            } else {
                DB::commit();

                //check if has cache cart items
                if (Cache::has('cartItems'.$userId)) {
                    //clear cache products
                    Cache::forget('cartItems'.$userId);
                    //end clear cache
                }


                $additionalMsg = config('app_messages.AdditionalSaveOrdersMsg').$order->user->email;

                $data['message'] = config('app_messages.SuccessSaveOrdersInCart').$orderNum.'.'.$additionalMsg;
                $statusCode = 200;

                // Send mail
                //Get order details
                $order = $this->order->where('id', $orderId)
                                ->with('user.addresses')
                                ->with('notes')->with('orderItems.product')
                                ->first();

                Mail::to($order->user->email)->send(new OrderMail($order));

                //remove customer cache if any
                if (Cache::has('orders'.$userId)) {
                    //clear cache products
                    Cache::forget('orders'.$userId);
                    //end clear cache
                }
                //end

                //Notify admin users for new order
                Helper::makeNotificationsToAdmin($orderId, 'New Order', 'Received new order.');
                //end notify
            }
        } else {
            $data['message'] = config('app_messages.CannotBeEmptyShoppingBag');
            $statusCode = 400;
        }
        return response()->json($data, $statusCode);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId, $bagId)
    {
        // return $id;
        $cartItem = $this->cartItem->where('user_id', $userId)->where('id', $bagId)->first();
   
        if ($cartItem) {
            $cartItem->quantity = $request->quantity ? $request->quantity : 0;
            $cartItem->variant_id = $request->variant_id ? $request->variant_id: $cartItem->variant_id;
            if ($cartItem->save()) {
                $data['message'] = config('app_messages.SuccessUpdateOrderQuantity');
                $statusCode = 200;

                //check if has cache cart items
                if (Cache::has('cartItems'.$userId)) {
                    //clear cache products
                    Cache::forget('cartItems'.$userId);
                    //end clear cache
                }
            } else {
                $data['message'] = config('app_messages.SomethingWentWrong');
                $statusCode = 200;
            }
        } else {
            $data['message'] = 'Item not found';
            $statusCode = 404;
        }
        return response()->json($data, $statusCode);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId, $bagId)
    {
        // return $id;
        $cartItem = $this->cartItem->where('user_id', $userId)->where('id', $bagId)->first();

        if ($cartItem) {
            //remove also on ongoing
            $deleteOngoing = $this->ongoingCheckout->where('user_id', $userId)
                                  ->where('variant_id', $cartItem->variant_id)
                                  ->where('product_id', $cartItem->product_id)
                                  ->first();

            if ($deleteOngoing) {
                $deleteOngoing->delete();
            }

            $cartItem->delete();

            //check if has cache cart items
            if (Cache::has('cartItems'.$userId)) {
                //clear cache products
                Cache::forget('cartItems'.$userId);
                //end clear cache
            }

            $data['message'] = config('app_messages.SuccessDeletedItemInShopppingBag');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.OrdertItemNotFound');
            $statusCode = 404;
        }
        return response()->json($data, $statusCode);
    }
}
