<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Wishlist\WishListStoreRequest;
use App\Http\Resources\WishListResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\Wishlist;
use App\Models\CartItem;
use Validator;
use Response;
use Config;

class WishlistController extends Controller
{

   

    //Declare models
    public function __construct(Wishlist $wishList, CartItem $cartItem)
    {
        $this->wishList = $wishList;
        $this->cartItem = $cartItem;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function getLiveWishlists(Request $request)
    {
       
        if (Cache::has('wishList'.request('page'))) {
            $wishList = Cache::get('wishList'.request('page'));
        } else {
            $wishList = Cache::remember('wishList'.request('page'), config('cache.cacheTime'), function () {
                return $this->wishList->with('customer')->paginate(10);
            });
        }
       
        if ($wishList) {
            $data =  WishListResource::collection($wishList);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoProductsAvailable');
            $statusCode = 200;
            
            return response()->json($data, $statusCode);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $userId)
    {

        if (Cache::has('wishList'.$userId)) {
            $wishList = Cache::get('wishList'.$userId);
        } else {
            $wishList = Cache::remember('wishList'.$userId, config('cache.cacheTime'), function () use ($userId) {
                return $this->wishList->where('user_id', $userId)->get();
            });
        }
       
        if ($wishList) {
            $data =  WishListResource::collection($wishList);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoProductsAvailable');
            $statusCode = 200;
            
            return response()->json($data, $statusCode);
        }
    }
    /**
     * Display customer public wishlist
     *
     * @return \Illuminate\Http\Response
     */
    public function publicCustomerWishlist(Request $request, $customerId)
    {
        $wishList = $this->wishList->where('user_id', $customerId)->get();
       
        if ($wishList) {
            $data =  WishListResource::collection($wishList);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoProductsAvailable');
            $statusCode = 200;
            
            return response()->json($data, $statusCode);
        }
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WishListStoreRequest $request, $userId)
    {
        // return $userId;
        //check if exist
        $wishList = $this->wishList->where('product_id', $request->product_id)
                                    ->where('variant_id', $request->variant_id)
                                    ->where('user_id', $userId)
                                    ->first();
        if ($wishList) {
            $data['message'] = config('app_messages.AlreadyInCustomerWishlist');
            $statusCode = 200;
        } else {
            $newWishList = new $this->wishList;
            $newWishList->user_id = $userId;
            $newWishList->product_id = $request->product_id;
            $newWishList->variant_id = $request->variant_id;
            $newWishList->quantity = 1;

            if ($newWishList->save()) {
                if (Cache::has('wishList'.$userId)) {
                    //clear cache products
                    Cache::forget('wishList'.$userId);
                    //end clear cache
                }

                $data['message'] = config('app_messages.SuccessAddedProductInWishlist');
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.SomethingWentWrong');
                $statusCode = 400;
            }
        }
        return Response::json($data, $statusCode);
    }

    /**
     * Save multiple products in wishlist
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMultipleProduct(Request $request, $userId)
    {
        // return $request->all();

        $wishLists = $request->wishlists;
        $user = $request->user('api');
        $userId = $user ?  $user->id: $userId;

        foreach ($wishLists as $key => $wishList) {
            $checkExist = $this->wishList->where('product_id', $wishList['product_id'])
                                    ->where('variant_id', $wishList['variant_id'])
                                    ->where('user_id', $userId)
                                    ->first();


            if ($checkExist) {
                $checkExist->quantity = $wishList['quantity'];
                $checkExist->update();

                $data['message'] = 'Updated customer wishlist content.';
                $statusCode = 200;
            } else {
                $newWishList = new $this->wishList;
                $newWishList->user_id = $userId;
                $newWishList->product_id = $wishList['product_id'];
                $newWishList->variant_id = $wishList['variant_id'];
                $newWishList->quantity = $wishList['quantity'];

                if ($newWishList->save()) {
                    $data['message'] = config('app_messages.SuccessAddedProductInWishlist');
                    $statusCode = 200;
                } else {
                    $data['message'] = config('app_messages.SomethingWentWrong');
                    $statusCode = 400;
                }
            }
        }

        if (Cache::has('wishList'.$userId)) {
            //clear cache products
            Cache::forget('wishList'.$userId);
            //end clear cache
        }

        return Response::json($data, $statusCode);
    }

    /**
     * Move selected product from wishlist to shopping bag
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function moveToShoppingBag(Request $request, $userId, $wishListId)
    {
        // return $request->all();
        $wishList = $this->wishList->where('id', $wishListId)->where('user_id', $userId)->first();

        if ($wishList) {
            //Check item if existing in the shopping bag
            $item = $this->cartItem->where('product_id', $wishList->product_id)
                                    ->where('variant_id', $wishList->variant_id)
                                    ->where('user_id', $userId)
                                    ->first();

            //Remove in wishlist and update quantity, variant in shopping bag if exist
            if ($item) {
                $statusCode = 200;
                $item->quantity = $wishList->quantity ? $wishList->quantity : 1;
                $item->update();

                $data['existing'] = true;

                $data['message'] = config('app_messages.ProductIsAlreadyInShoppingBagsUpdateContent');
            } else {
                $newItemInCart = new $this->cartItem;
                $newItemInCart->user_id = $userId;
                $newItemInCart->product_id = $wishList->product_id;
                $newItemInCart->quantity = $wishList->quantity;
                $newItemInCart->variant_id = $wishList->variant_id;
                $newItemInCart->save();

                $data['existing'] = false;
            }
            //if success force delete moved wish list from the database.
            $wishList->delete();

            //check if has cache wishlist
            if (Cache::has('wishList'.$userId)) {
                //clear cache products
                Cache::forget('wishList'.$userId);
                //end clear cache
            }
            //check if has cache cart items
            if (Cache::has('cartItems'.$userId)) {
                //clear cache products
                Cache::forget('cartItems'.$userId);
                //end clear cache
            }

            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessMovedToShoppingBag');
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.WishlistNotFound');
        }


        return Response::json(['data' => $data], $statusCode);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId, $wishListId)
    {
        // return $id;
        $wishListItem = $this->wishList->where('user_id', $userId)->where('id', $wishListId)->first();

        if ($wishListItem) {
            $wishListItem->quantity = $request->quantity ? $request->quantity : 0;
            $wishListItem->variant_id = $request->variant_id ? $request->variant_id : $wishListItem->variant_id;

            if ($wishListItem->update()) {
                $data['message'] = config('app_messages.SuccessUpdatedWishlist');
                $statusCode = 200;


                //check if has cache wishlist
                if (Cache::has('wishList'.$userId)) {
                    //clear cache products
                    Cache::forget('wishList'.$userId);
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
    public function destroy($userId, $wishListId)
    {
        // return $userId;
        $wishList = $this->wishList->where('user_id', $userId)->where('id', $wishListId)->first();

        if ($wishList) {
            //if wishlist exist then success force delete from the database.
            $wishList->delete();
            $statusCode = 200;
            $data['message'] = config('app_messages.RemoveProductFromWishList');

            //check if has cache wishlist
            if (Cache::has('wishList'.$userId)) {
                //clear cache products
                Cache::forget('wishList'.$userId);
                //end clear cache
            }
        } else {
            $data['message'] = config('app_messages.WishlistNotFound');
            $statusCode = 404;
        }

        return Response::json(['data' => $data], $statusCode);
    }
}
