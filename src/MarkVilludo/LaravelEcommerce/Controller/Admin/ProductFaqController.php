<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Api\Faq\FaqStoreRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductFaq;
use App\Models\Product;
use Validator;
use Response;
use Session;

class ProductFaqController extends Controller
{

   
    //construct model variable
    public function __construct(Product $product, ProductFaq $productFaq)
    {
        $this->product = $product;
        $this->productFaq = $productFaq;
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
        return view('admin.product.faq.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $productId)
    {
        // return $productId;
        $rules = [
            'title' => 'required|string',
            'description' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $message = json_encode([$validator->errors()]);
            $statusCode = 422;
        } else {
            $newFaq = new $this->productFaq;
            $newFaq->user_id = $request->user()->id;
            $newFaq->product_id = $productId;
            $newFaq->title = $request->title;
            $newFaq->description = $request->description;
            
            if ($newFaq->save()) {
                $statusCode = 200;
                $message = config('app_messages.SuccessCreateProductInfo');
            } else {
                $statusCode = 400;
                $message = config('app_messages.SomethingWentWrong');
            }
        }
        Session::flash('message', $message);
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($productId, $faqId)
    {
        $data['faq'] = $this->productFaq->where('id', $faqId)->where('product_id', $productId)->first();
        $product = $this->product->find($productId);

        $data['product'] = $product;
        $data['message'] = null;
        return view('admin.product.faq.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $productId, $faqId)
    {
        // update product Faq.
        $rules = [
            'title' => 'required|string',
            'description' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $message = json_encode([$validator->errors()]);
            $statusCode = 422;
        } else {
            //mostly the descriptions answer question
            $productFaq = $this->productFaq->where('product_id', $productId)->where('id', $faqId)->first();

            if ($productFaq) {
                $productFaq->title = $request->title ? $request->title : $productFaq->title;
                $productFaq->description = $request->description;
                $productFaq->update();
                
                $statusCode = 200;
                $message = config('app_messages.SuccessUpdateProductInfo');
            } else {
                $statusCode = 400;
                $message = config('app_messages.SomethingWentWrong');
            }
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
    public function destroy($productId, $faqId)
    {
        // return $variantId;
        $productFaq = $this->productFaq->where('id', $faqId)->where('product_id', $productId)->first();

        if ($productFaq) {
            if (!$productFaq->deleted_at) {
                $productFaq->delete();

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
