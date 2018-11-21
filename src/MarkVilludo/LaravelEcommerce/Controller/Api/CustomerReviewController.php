<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Product\ProductStoreReview;
use App\Http\Resources\ReviewsResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use App\Models\Product;

class CustomerReviewController extends Controller
{

   

    public function __construct(ProductReview $productReview, Product $product)
    {
        $this->productReview = $productReview;
        $this->product = $product;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($userId)
    {
        //get latest reviews
        $productReviews = $this->productReview->where('user_id', $userId)->orderBy('created_at', 'desc')->get();

        if ($productReviews) {
            $data = ReviewsResource::collection($productReviews);
            return $data;
        } else {
            $data['message'] = config('app_messages.TherIsNoReviewsAvailable');
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
    public function store(ProductStoreReview $request, $userId)
    {
        // return $request->all();
        $newProductReview = new $this->productReview;
        $newProductReview->user_id = $userId;
        $newProductReview->rate = $request->rate;
        $newProductReview->title = $request->title;
        $newProductReview->description = $request->description;
        $newProductReview->product_id = $request->product_id;

        //get product details and increment by 1 the product rating
        $product = $this->product->find($request->product_id);
        $product->increment('ratings');

        if ($newProductReview->save()) {
            if (Cache::has('productDetails'.$request->product_id)) {
                //clear cache products
                Cache::forget('productDetails'.$request->product_id);
                //end clear cache
            }
            //product average
            $averageRatings = $this->productReview->getRatingsAverage($request->product_id);
            $product = $this->product->find($request->product_id);
            $product->ratings = $averageRatings->average_ratings;
            $product->update();

            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessCreatedProductReview');
        } else {
            $statusCode = 200;
            $data['message'] = config('app_messages.SomethingWentWrong');
        }
        return response()->json($data, $statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($userId, $reviewId)
    {
        // show review details
        $checkIfExist = $this->productReview->checkIfExisting($userId, $reviewId);

        if ($checkIfExist) {
            $statusCode = 200;
            return new ReviewsResource($checkIfExist);
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.NotFoundReview');
            return response()->json($data, $statusCode);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductStoreReview $request, $userId, $reviewId)
    {
        $checkIfExist = $this->productReview->checkIfExisting($userId, $reviewId);
        
        if ($checkIfExist) {
            //update current product review
            $checkIfExist->rate = $request->rate;
            $checkIfExist->title = $request->title;
            $checkIfExist->description = $request->description;
            $checkIfExist->created_at = date('Y-m-d H:i:s');

            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessUpdatedProductReview');
            
            if ($checkIfExist->update()) {
                if (Cache::has('productDetails'.$request->product_id)) {
                    //clear cache products
                    Cache::forget('productDetails'.$request->product_id);
                    //end clear cache
                }
                //product average
                $averageRatings = $this->productReview->getRatingsAverage($request->product_id);
                $product = $this->product->find($request->product_id);
                $product->ratings = $averageRatings->average_ratings;
                $product->update();

                $statusCode = 200;
                $data['message'] = config('app_messages.SuccessUpdatedProductReview');
            } else {
                $statusCode = 400;
                $data['message'] = config('app_messages.SomethingWentWrong');
            }
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.NotFoundReview');
        }
     
        return response()->json($data, $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId, $reviewId)
    {
        // return $wishListId;
        $productReview = $this->productReview->where('user_id', $userId)->where('id', $reviewId)->first();

        //get product details and increment by 1 the product rating
        $product = $this->product->find($productReview->product_id);
        $product->decrement('ratings');

        if ($productReview) {
             //if productReview exist then success force delete from the database.
            $productReview->delete();

            if (Cache::has('productDetails'.$productReview->product_id)) {
                //clear cache products
                Cache::forget('productDetails'.$productReview->product_id);
                //end clear cache
            }
            
            $statusCode = 200;
            $data['message'] = config('app_messages.RemoveProductFromReviews');
        } else {
            $data['message'] = config('app_messages.NotFoundReview');
            $statusCode = 404;
        }

        return response()->json($data, $statusCode);
    }
}
