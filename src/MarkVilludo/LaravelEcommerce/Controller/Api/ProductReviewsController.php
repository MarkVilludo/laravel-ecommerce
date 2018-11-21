<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Product\ProductStoreReview;
use App\Http\Resources\ReviewsResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductReviewsController extends Controller
{


    public function __construct(ProductReview $productReview, Product $product)
    {
        $this->productReview = $productReview;
        $this->product = $product;
    }
    
    /**
     * Get product reviews paginate
     *
     * @return \Illuminate\Http\Response
     */
    public function productReviews($productId)
    {
        // return $productId;
        $productReviews = $this->productReview->where('product_id', $productId)->paginate(2);

        if ($productReviews) {
            $data =  ReviewsResource::collection($productReviews);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoProductReviewsAvailable');
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
    public function store(ProductStoreReview $request, $productId)
    {
        // return $request->all();
        $newProductReview = new $this->productReview;
        $newProductReview->user_id = $request->user()->id;
        $newProductReview->product_id = $productId;
        $newProductReview->rate = $request->rate;
        $newProductReview->title = $request->title;
        $newProductReview->description = $request->description;
        
        if ($newProductReview->save()) {
            if (Cache::has('productDetails'.$productId)) {
                //clear cache products
                Cache::forget('productDetails'.$productId);
                //end clear cache
            }
            //product average
            $averageRatings = $this->productReview->getRatingsAverage($productId);

            if ($averageRatings) {
                $product = $this->product->find($productId);
                $product->ratings = $averageRatings->average_ratings;
                $product->update();
            }

            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessCreatedProductReview');
        } else {
            $statusCode = 400;
            $data['message'] = config('app_messages.SomethingWentWrong');
        }

        return response()->json($data, $statusCode);
    }
     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductStoreReview $request, $productId, $reviewId)
    {
        $productReview = $this->productReview->where('product_id', $productId)->where('id', $reviewId)->first();

        if ($productReview) {
            $productReview->rate = $request->rate;
            $productReview->title = $request->title ? $request->title : $productReview->title;
            $productReview->description = $request->description;
            $productReview->created_at = date('Y-m-d H:i:s');
            
            $productReview->update();

            if (Cache::has('productDetails'.$productId)) {
                //clear cache products
                Cache::forget('productDetails'.$productId);
                //end clear cache
            }

            //product average
            $averageRatings = $this->productReview->getRatingsAverage($productId);
            $product = $this->product->find($productId);
            $product->ratings = $averageRatings->average_ratings;
            $product->update();

            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessUpdatedProductReview');
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.NotFoundReview');
        }

        return response()->json($data, $statusCode);
    }
}
