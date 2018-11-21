<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\Wishlist;
use Response;
use Session;

class WishlistController extends Controller
{
    //
    //construct model variable
    public function __construct(Wishlist $wishList)
    {
        $this->wishList = $wishList;
    }

    /**
     * Display a listing of available stores.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get data from api controller
        return view('admin.wishlist.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($wishListId)
    {
        //forget cache
        Cache::forget('wishList');
        //end
        // return $wishListId;
        $wishList = $this->wishList->where('id', $wishListId)->first();

        if ($wishList) {
             //if wishlist exist then success force delete from the database.
            $wishList->delete();
            $statusCode = 200;
            $data['message'] = config('app_messages.RemoveProductFromWishList');
        } else {
            $data['message'] = config('app_messages.WishlistNotFound');
            $statusCode = 404;
        }

        return Response::json(['data' => $data], $statusCode);
    }
}
