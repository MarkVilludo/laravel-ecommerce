<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\CustomerAddress;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Traits\VoucherTrait;
use App\Models\PackageItem;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Wishlist;
use App\Models\Package;
use App\Mail\OrderMail;
use App\Models\Product;
use App\Models\Order;
use Validator;
use App\User;
use Response;
use Config;

class ShoppingBagController extends Controller
{

   
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
        Wishlist $wishList
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
    }
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Get data from API
        return view('admin.shopping_bag.index');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
