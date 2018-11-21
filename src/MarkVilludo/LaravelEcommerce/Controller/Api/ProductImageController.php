<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductImageResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Validator;
use Response;
use Storage;
use File;
use Config;

class ProductImageController extends Controller
{

   
    //Declaration moddel
    public function __construct(ProductImage $productImage, ProductVariant $productVariant)
    {
        $this->productImage = $productImage;
        $this->productVariant = $productVariant;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($productId)
    {
        // return $productId;
        $productImage = ProductImageResource::collection($this->productImage->where('product_id', $productId)->get());
        if ($productImage) {
            $status_code = 200;
            $data['images'] = $productImage;
            $data['message'] = config('app_messages.ShowProductImages');
        } else {
            $status_code = 404;
            $data['message'] = config('app_messages.NoImageFoundInProduct');
        }
        return Response::json($data, $status_code);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $productId)
    {
        // return $request->all();
        if ($request->file('file')) {
            $filename = 'IMG_'.rand(23, 3123123).".".$request->file('file')->getClientOriginalExtension();
            $path = $request->file('file')->storeAs('public/products', $filename);

            $newProductImage = new $this->productImage;
            $newProductImage->file_name = $filename;
            $newProductImage->product_id = $productId;
            $newProductImage->page_preview = $request->page_preview;
            $newProductImage->path = 'storage'.'/products'.'/'.$filename;
            if ($newProductImage->save()) {
                $data['message'] = config('app_messages.SuccessUploadImage');
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.SomethingWentWrong');
                $statusCode = 400;
            }
        } else {
            $data['message'] = config('app_messages.ImageIsRequired');
            $statusCode = 400;
        }
        return Response::json($data, $statusCode);
    }

        /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeVariantImage(Request $request, $productId, $variantId)
    {
        //
        // return $variantId;
        if ($request->file('file')) {
            $filename = 'IMG_'.rand(23, 3123123).".".$request->file('file')->getClientOriginalExtension();
            $path = $request->file('file')->storeAs('public/products', $filename);

            $newProductImage = new $this->productImage;
            $newProductImage->file_name = $filename;
            $newProductImage->product_id = $productId;
            $newProductImage->path = 'storage'.'/products'.'/'.$filename;
            if ($newProductImage->save()) {
                //update variant image
                $productVariant = $this->productVariant->where('product_variant_id', $variantId)
                                                       ->where('product_id', $productId)
                                                       ->first();

                $productVariant->product_image_id = $newProductImage['id'];
                $productVariant->save();

                $data['message'] = config('app_messages.SuccessUploadImage');
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.SomethingWentWrong');
                $statusCode = 400;
            }
        } else {
            $data['message'] = config('app_messages.ImageIsRequired');
            $statusCode = 400;
        }
        return Response::json($data, $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($productId, $imageId)
    {
        // return $id;
        $product = $this->productImage->where('product_id', $productId)->where('id', $imageId)->first();

        if ($product) {
            if (!$product->deleted_at) {
                $product->delete();

                //remove existing image in storage
                Storage::delete('public/products/'.$product->file_name);

                $data['message'] = config('app_messages.SuccessDeletedProduct');
                $status_code = 200;
            } else {
                $data['message'] = config('app_messages.AlreadyDeletedProductImage');
                $status_code = 400;
            }
        } else {
            $data['message'] = config('app_messages.NotFoundImage');
            $status_code = 404;
        }
        return Response::json($data, $status_code);
    }
}
