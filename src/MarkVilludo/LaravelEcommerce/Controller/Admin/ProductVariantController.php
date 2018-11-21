<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Api\Product\ProductStoreVariantRequest;
use App\Http\Resources\ProductVariantResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use App\Models\Product;
use App\Helpers\Helper;

use Validator;
use Response;
use Config;
use Session;
use Storage;
use File;

class ProductVariantController extends Controller
{

   
    //construct model variable
    public function __construct(Product $product, ProductVariant $productVariant, ProductImage $productImage)
    {
        $this->product = $product;
        $this->productVariant = $productVariant;
        $this->productImage = $productImage;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($productId)
    {
        $product = $this->product->find($productId);
        $data['product'] = $product;
        $data['message'] = null;
        return view('admin.product.variant.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreVariantRequest $request, $productId)
    {
        // return $productId;

        $newProductVariant = new $this->productVariant;
        $newProductVariant->size = $request->size;
        $newProductVariant->color = $request->color;

        $newProductVariant->product_id = $productId;
        $newProductVariant->grams = $request->grams;
        $newProductVariant->barcode = $request->barcode;
        $newProductVariant->weight = $request->weight;
        $newProductVariant->weight_unit = $request->weight_unit;
        $newProductVariant->sku = $request->sku;

        $newProductVariant->inventory = $request->inventory;
        
        if ($newProductVariant->save()) {
            if (Cache::has('productDetails'.$product->id)) {
                //clear cache products
                Cache::forget('productDetails'.$product->id);
                //end clear cache
            }
            //check product image exist
            if ($request->file('file')) {
                //call resize and crop images function
                    $file = $request->file('file');
                    $origFilePath = '/storage/products';
                    $filename = md5($file->getClientOriginalName());
                    $filetype = $file->getClientOriginalExtension();
                    Helper::storeImages($file, $origFilePath);
                //end
                $newProductImage = new $this->productImage;
                $newProductImage->file_name = $filename.'.'.$filetype;
                $newProductImage->product_id = $productId;
                $newProductImage->product_variant_id = $newProductVariant->id;
                $newProductImage->path = $origFilePath;
                $newProductImage->save();
            }

            $message = config('app_messages.SuccessCreateProductVariant');
            $statusCode = 200;
        } else {
            $message = config('app_messages.SomethingWentWrong');
            $statusCode = 400;
        }
        Session::flash('message', $message);
        return redirect()->back();
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
    public function edit($productId, $vartiantID)
    {
        $data['variant'] = $this->productVariant->find($vartiantID);
        $product = $this->product->find($productId);

        $data['product'] = $product;
        $data['message'] = null;
        return view('admin.product.variant.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductStoreVariantRequest $request, $productId, $variantId)
    {
    //
        $productVariant = $this->productVariant->where('id', $variantId)->where('product_id', $productId)->first();

        if ($productVariant) {
            $productVariant->size = $request->size;
            $productVariant->color = $request->color;
            $productVariant->grams = $request->grams;
            $productVariant->barcode = $request->barcode;
            $productVariant->weight = $request->weight;
            $productVariant->weight_unit = $request->weight_unit;
            $productVariant->sku = $request->sku;

            $productVariant->inventory = $request->inventory;

            // for upload image
            $file = $request->file('file');
            $origFilePath = '/storage/products';
            $filename = md5($file->getClientOriginalName());
            $filetype = $file->getClientOriginalExtension();


            //check product image exist
            if ($request->file('file')) {
                //generated file name
                $updateProductImage = $this->productImage->where('product_id', $productId)
                                                        ->where('id', $variantId)
                                                        ->first();
                if ($updateProductImage) {
                    // $path = $request->file('file')->storeAs('public/products', $filename);

                    // return $updateProductImage->file_name;
                    //remove existing image in storage
                    //call resize and crop images function
                    Helper::storeImages($file, $origFilePath);
                    //end
                    //remove existing image in storage
                    Storage::delete('public/products/'.$updateProductImage->file_name);
                    Storage::delete('public/products/medium/'.$updateProductImage->file_name);
                    Storage::delete('public/products/small/'.$updateProductImage->file_name);
                    Storage::delete('public/products/xsmall/'.$updateProductImage->file_name);


                    $updateProductImage->file_name = $filename.'.'.$filetype;
                    $updateProductImage->product_variant_id = $variantId;
                    $updateProductImage->product_id = $productId;
                    $updateProductImage->path = $origFilePath;
                    $updateProductImage->update();
                } else {
                    //call resize and crop images function
                    Helper::storeImages($file, $origFilePath);
                    //end

                    $newProductImage = new $this->productImage;
                    $newProductImage->file_name = $filename.'.'.$filetype;
                    $newProductImage->product_id = $productId;
                    $newProductImage->product_variant_id = $variantId;
                    $newProductImage->path =  $origFilePath;
                    $newProductImage->save();
                }
            }
            
            if ($productVariant->update()) {
                if (Cache::has('productDetails'.$productId)) {
                    //clear cache products
                    Cache::forget('productDetails'.$productId);
                    //end clear cache
                }
                
                $message = config('app_messages.SuccessUpdatedProductVariant');
                $statusCode = 200;
            } else {
                $message = config('app_messages.SomethingWentWrong');
                $statusCode = 400;
            }
            if (Cache::has('productDetails'.$productId)) {
                //clear cache products
                Cache::forget('productDetails'.$productId);
                //end clear cache
            }
        } else {
            $message = config('app_messages.NotFoundProductVariant');
            $statusCode = 404;
        }
        Session::flash('message', $message);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($productId, $variantId)
    {
        // return $productId;
        $productVariant = $this->productVariant->where('id', $variantId)->where('product_id', $productId)->first();

        if ($productVariant) {
            if (!$productVariant->deleted_at) {
                $productVariant->delete();

                //remove also image attach in product variant
                $productImage = $this->productImage->find($productVariant->product_image_id);
                if ($productImage) {
                    $productImage->file_name;

                    //remove existing image in storage
                    Storage::delete('public/products/'.$productImage->file_name);
                    $productImage->delete();
                }

                if (Cache::has('productDetails'.$productId)) {
                    //clear cache products
                    Cache::forget('productDetails'.$productId);
                    //end clear cache
                }

                $message = config('app_messages.SuccessDeletedProductVariant');
                $statusCode = 200;
            } else {
                $$message = config('app_messages.ProductVariantAlreadyDeleted');
                $statusCode = 400;
            }
            if (Cache::has('products1')) {
                //clear cache products
                Cache::forget('products1');
                //end clear cache
            }
        } else {
            $message = config('app_messages.NotFoundProductVariant');
            $statusCode = 404;
        }
        Session::flash('message', $message);
        return redirect()->back();
    }
}
