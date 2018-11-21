<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Api\Product\ProductInfoUpdateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductInfo;
use App\Models\Product;
use Validator;
use Response;
use Session;

class ProductInfoController extends Controller
{


    //construct model variable
    public function __construct(Product $product, ProductInfo $productInfo)
    {
        $this->product = $product;
        $this->productInfo = $productInfo;
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
        return view('admin.product.info.create', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($productId, $infoId)
    {
        $data['info'] = $this->productInfo->where('id', $infoId)->where('product_id', $productId)->first();
        $product = $this->product->find($productId);

        $data['product'] = $product;
        $data['message'] = null;
        return view('admin.product.info.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductInfoUpdateRequest $request, $productId, $infoId)
    {
        // update product info.
        $productInfo = $this->productInfo->where('product_id', $productId)->where('id', $infoId)->first();

        if ($productInfo) {
            $productInfo->title = $request->title ? $request->title : $productInfo->title;
            $productInfo->description = $request->description;
            $productInfo->update();
            
            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessUpdateProductInfo');
        } else {
            $statusCode = 400;
            $data['message'] = config('app_messages.SomethingWentWrong');
        }

        return response()->json($data, $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($productId, $infoId)
    {
        // return $variantId;
        $productInfo = $this->productInfo->where('id', $infoId)->where('product_id', $productId)->first();

        if ($productInfo) {
            if (!$productInfo->deleted_at) {
                $productInfo->delete();

                $message = config('app_messages.SuccessDeletedProductinfo');
                $statusCode = 200;
            } else {
                $$message = config('app_messages.ProductVariantAlreadyDeleted');
                $statusCode = 400;
            }
        } else {
            $message = config('app_messages.NotFoundProductVariant');
            $statusCode = 404;
        }
        Session::flash('message', $message);
        return redirect()->back();
    }
}
