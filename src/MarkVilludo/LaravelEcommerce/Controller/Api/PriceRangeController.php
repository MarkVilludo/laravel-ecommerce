<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Product\PriceRangeStoreRequest;
use App\Http\Resources\PriceRangeResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PriceRange;

class PriceRangeController extends Controller
{

    public function __construct(PriceRange $priceRange)
    {
        $this->priceRange = $priceRange;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $priceRange =  $this->priceRange->paginate(10);

        if ($priceRange) {
            $data =  PriceRangeResource::collection($priceRange);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoProductsAvailable');
            $statusCode = 200;
            
            return response()->json($data, $statusCode);
        }
    }

    /**
     * Save nenw price range
     *
     * @return \Illuminate\Http\Response
     */
    public function store(PriceRangeStoreRequest $request)
    {
        // return $request->all();
        $priceFrom = $request->price_from;
        $priceTo = $request->price_to;

        if ($priceFrom >= $priceTo) {
            $statusCode = 400;
            $data['status'] = 'success';
            $data['message'] = config('app_messages.PricefromMustLessThanPriceTo');
        } else {
            $newPriceRange = new $this->priceRange;
            $newPriceRange->from = $priceFrom;
            $newPriceRange->to = $priceTo;
            $newPriceRange->save();
            
            $statusCode = 200;
            $data['message'] = config('app_messages.SuccesSavedPriceRange');
        }
        return response()->json($data, $statusCode);
    }
     /**
     * Update price range
     *
     * @return \Illuminate\Http\Response
     */
    public function update(PriceRangeStoreRequest $request, $id)
    {
        // return $request->all();
        $priceFrom = $request->price_from;
        $priceTo = $request->price_to;

        if ($priceFrom >= $priceTo) {
            $statusCode = 400;
            $data['status'] = 'success';
            $data['message'] = config('app_messages.PricefromMustLessThanPriceTo');
        } else {
            $priceRange = $this->priceRange->find($id);
            $priceRange->from = $priceFrom;
            $priceRange->to = $priceTo;
            $priceRange->update();
            
            $statusCode = 200;
            $data['message'] = config('app_messages.SuccesUpdatedPriceRange');
        }
        return response()->json($data, $statusCode);
    }
    /**
     * Remove price range
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // return $id;
        $priceRange = $this->priceRange->find($id);
        if ($priceRange) {
            if ($priceRange->delete()) {
                $statusCode = 200;
                $data['message'] = config('app_messages.SuccesDeletedPriceRange');
            } else {
                $statusCode = 400;
                $data['message'] = config('app_messages.SomethingWentWrong');
            }
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.NotFoundJournalCategory');
        }
        
        return response()->json($data, $statusCode);
    }
}
