<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\FBT\FBTUpdateRequest;
use App\Http\Requests\Api\FBT\FBTStoreRequest;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Resources\FBTResource;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\FBTProduct;
use App\Models\FBT;
use Validator;

class FBTController extends Controller
{
    public function __construct(FBT $fbt, FBTProduct $fbtProduct)
    {
        $this->fbt = $fbt;
        $this->fbtProduct = $fbtProduct;
    }
    public function index()
    {
        $fbt = $this->fbt->with('fbtProducts.product')->paginate(10);

        if ($fbt) {
            $data = FBTResource::collection($fbt);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoFBTFound');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }

    /**
     * Search fbt
     *
     * @return \Illuminate\Http\Response
    */
    public function searchFBT(Request $request)
    {
        // return $request->all();
        $fbt = $this->fbt->getByName($request->name)->with('fbtProducts.product')->paginate(10);

        if ($fbt) {
            $data = FBTResource::collection($fbt);
            return $data;
        } else {
            $data['message'] = config('app_messages.TherIsNoFbtAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }
     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // return $id;
        $fbt = $this->fbt->where('id', $id)->with('fbtProducts.product')->first();

        if ($fbt) {
            $statusCode = 200;
            $productItems = [];

            foreach ($fbt->fbtProducts as $key => $fbt_product) {
                $productItems[] = $fbt_product->product;
            }
            $data['fbt'] = $fbt;
            $data['product_items'] = $productItems;
            // return new FBTResource($fbt);
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.FBTNotFound');
        }
        return response()->json($data, $statusCode);
    }

    public function store(FBTStoreRequest $request)
    {
        // return $request->all();
        $newFBT = new $this->fbt;
        $newFBT->name = $request->name;

        //clear cache products
        Cache::forget('products');
        //end clear cache

        if ($newFBT->save()) {
            foreach ($request->products as $key => $product) {
                $newFBTProducts = new $this->fbtProduct;
                $newFBTProducts->fbt_id = $newFBT->id;
                $newFBTProducts->product_id = $product['id'];
                $newFBTProducts->save();
            }

            $data['message'] = config('app_messages.SuccessCreatedSetFBT');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.SomethingWentWrong');
            $statusCode = 400;
        }
        return response()->json($data, $statusCode);
    }
    //update fbt details

    public function update(FBTUpdateRequest $request, $id)
    {
        // return $id;
        $fbt = $this->fbt->find($id);
        $fbt->name = $request->name;

        if ($fbt->update()) {
            //clear cache products
            Cache::forget('products');
            //end clear cache

            //remove all fbt products existing before insert all again
            $removeAllFTBProducts = $this->fbtProduct->where('fbt_id', $id)->delete();

            foreach ($request->products as $key => $product) {
                $newFBTProducts = new $this->fbtProduct;
                $newFBTProducts->fbt_id = $id;
                $newFBTProducts->product_id = $product['id'];
                $newFBTProducts->save();
            }

            $data['message'] = config('app_messages.SuccessUpdatedSetFBT');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.SomethingWentWrong');
            $statusCode = 400;
        }
        return response()->json($data, $statusCode);
    }
    /**
     * Remove FBT
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($fbtId)
    {
        //clear cache products
        Cache::forget('products');
        //end clear cache
        
        $fbt = $this->fbt->find($fbtId);
        if ($fbt) {
            if ($fbt->delete()) {
                $removeAllFTBProducts = $this->fbtProduct->where('fbt_id', $fbtId)->delete();

                $data['message'] = config('app_messages.SuccessDeletedFBT');
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.SomethingWentWrong');
                $statusCode = 400;
            }
        } else {
            $data['message'] = config('app_messages.NoFBTFound');
            $statusCode = 404;
        }
        return response()->json($data, $statusCode);
    }
}
