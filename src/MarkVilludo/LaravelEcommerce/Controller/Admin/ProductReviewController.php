<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use App\Models\Product;
use Response;
use Session;

class ProductReviewController extends Controller
{
    //construct model variable
    public function __construct(Product $product, ProductReview $productReview)
    {
        $this->product = $product;
        $this->productReview = $productReview;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($productId)
    {
        // return $productId;
        $product = $this->product->find($productId);
        $data['product'] = $product;
        $data['message'] = null;
        return view('admin.product.review.create', $data);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($productId, $reviewId)
    {
        $data['review'] = $this->productReview->where('id', $reviewId)->where('product_id', $productId)->first();
        $product = $this->product->with('defaultImage')->find($productId);

        $data['product'] = $product;
        $data['message'] = null;
        return view('admin.product.review.edit', $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($productId, $reviewId)
    {
        // return $variantId;
        if (Cache::has('productDetails'.$productId)) {
            //clear cache products
            Cache::forget('productDetails'.$productId);
            //end clear cache
        }
        $productReview = $this->productReview->where('id', $reviewId)->where('product_id', $productId)->first();

        //get product details and increment by 1 the product rating
        $product = $this->product->find($productId);
        $product->decrement('ratings');

        if ($productReview) {
            if (!$productReview->deleted_at) {
                $productReview->delete();

                $message = config('app_messages.SuccessDeletedProductReviews');
                $statusCode = 200;
            } else {
                $$message = config('app_messages.SomethingWentWrong');
                $statusCode = 400;
            }
        } else {
            $message = config('app_messages.NotFoundProductReview');
            $statusCode = 404;
        }

        Session::flash('message', $message);
        return redirect()->back();
    }
}
