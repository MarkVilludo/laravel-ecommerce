<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Product\ProductStoreVariantRequest;
use App\Http\Resources\ProductVariantResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\ProductVariantColor;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use App\Models\Product;

use Storage;
use Validator;
use Response;
use Config;

class ProductVariantController extends Controller
{


    //construct model variable
    public function __construct(
        ProductVariant $productVariant,
        ProductImage $productImage,
        ProductVariantColor $productVariantColor,
        Product $product
    ) {
        $this->productVariant = $productVariant;
        $this->productImage = $productImage;
        $this->productVariantColor = $productVariantColor;
        $this->product = $product;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($productId)
    {
        // return $productId;
        return $productVariant = $this->productVariant->where('product_id', $productId)->paginate(10);
    }

    public function getAvailableColors($categoriesId)
    {
        $variants = $this->productVariantColor->select('color')->where('category_id', $categoriesId)
                                                               ->distinct('color')
                                                               ->get();

        $data['data'] = $variants;
        $statusCode = 200;
        if ($variants) {
            $data['message'] = config('app_messages.ShowsAvailableVariantColors');
        } else {
            $data['message'] = config('app_messages.NoVariantColorsAvailable');
        }

        return response()->json($data, $statusCode);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreVariantRequest $request, $productId)
    {
        // return $request->all();
        // return $productId;
        //get product details
        $product = $this->product->find($productId);

        $newProductVariant = new $this->productVariant;
        $newProductVariant->product_id = $productId;
        $newProductVariant->inventory = $request->inventory;

        if ($newProductVariant->save()) {
            if (Cache::has('productDetails'.$productId)) {
                //clear cache products
                Cache::forget('productDetails'.$productId);
                //end clear cache
            }
            //save product variant images
            foreach ($request->images as $key => $image) {
                $newProductImage = new $this->productImage;
                $newProductImage->file_name = $image['file_name'];
                $newProductImage->product_id = $productId;
                $newProductImage->product_variant_id = $newProductVariant->id;
                $newProductImage->path = '/storage/products';
                $newProductImage->save();
            }
            //save variant colors
            foreach ($request->colors as $key => $color) {
                $newProductColor = new $this->productVariantColor;
                $newProductColor->category_id = $product->child_sub_category_id;
                $newProductColor->variant_id = $newProductVariant->id;
                $newProductColor->color = $color;
                $newProductColor->save();
            }

            $data['message'] = config('app_messages.SuccessCreateProductVariant');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.SomethingWentWrong');
            $statusCode = 400;
        }
        return Response::json($data, $statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getVariantDetails($id)
    {
        $data['data'] = new ProductVariantResource($this->productVariant->where('id', $id)->with('colors')->first());
        if ($data['data']) {
            $statusCode = 200;
            $data['message'] = 'Shows variant details';
        } else {
            $statusCode = 404;
            $data['message'] = 'Product variant not found.';
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
    public function update(ProductStoreVariantRequest $request, $variantId)
    {
        

        $productVariant = $this->productVariant->find($variantId);
        //get product details
        $product = $this->product->find($productVariant->product_id);

        if ($productVariant) {
            $productVariant->inventory = $request->inventory;
            if ($productVariant->save()) {
                //Update also variants colors
                //remove first all variant colors then attach again new list
                $this->productVariantColor->where('variant_id', $variantId)->delete();

                foreach ($request->colors as $key => $value) {
                    $newProductVariantColor = new $this->productVariantColor;
                    $newProductVariantColor->category_id = $product->child_sub_category_id;
                    $newProductVariantColor->variant_id = $variantId;
                    $newProductVariantColor->color = $value;
                    $newProductVariantColor->save();
                }

                if (Cache::has('productDetails'.$productVariant->product_id)) {
                    //clear cache products
                    Cache::forget('productDetails'.$productVariant->product_id);
                    //end clear cache
                }

                $data['message'] = config('app_messages.SuccessUpdatedProductVariant');
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.SomethingWentWrong');
                $statusCode = 400;
            }
        } else {
            $data['message'] = config('app_messages.NotFoundProductVariant');
            $statusCode = 404;
        }
        return Response::json($data, $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($productId, $variantId)
    {
        // return $variantId;
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
                //delete also product colors
                $this->productVariantColor->where('variant_id', $variantId)->delete();
                if (Cache::has('productDetails'.$productId)) {
                    //clear cache products
                    Cache::forget('productDetails'.$productId);
                    //end clear cache
                }

                $data['message'] = config('app_messages.SuccessDeletedProductVariant');
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.ProductVariantAlreadyDeleted');
                $statusCode = 400;
            }
        } else {
            $data['message'] = config('app_messages.NotFoundProductVariant');
            $statusCode = 404;
        }
        return Response::json($data, $statusCode);
    }


     /**
     * Remove variant image
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeVariantImage($variantId, $imageId)
    {
        $productVariant = $this->productImage->where('id', $imageId)->where('product_variant_id', $variantId)->first();

        if ($productVariant) {
            //if image exist then success force delete from the database.
            $productVariant->delete();
            $statusCode = 200;

            //remove existing image in storage
            Storage::delete('public/products/'.$productVariant->file_name);
            Storage::delete('public/products/xsmall/'.$productVariant->file_name);
            Storage::delete('public/products/small/'.$productVariant->file_name);
            Storage::delete('public/products/medium/'.$productVariant->file_name);

            if (Cache::has('productDetails'.$productVariant->product_id)) {
                //clear cache products
                Cache::forget('productDetails'.$productVariant->product_id);
                //end clear cache
            }


            $data['message'] = config('app_messages.ProductVariantImageDeleted');
        } else {
            $data['message'] = config('app_messages.NotFoundJournalSliderImage');
            $statusCode = 404;
        }

        return Response::json(['data' => $data], $statusCode);

       
        return Response::json($data, $statusCode);
    }
}
