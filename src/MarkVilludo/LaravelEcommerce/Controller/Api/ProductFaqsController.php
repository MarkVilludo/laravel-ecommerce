<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Faq\FaqStoreRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductFaq;
use Response;
use Config;

class ProductFaqsController extends Controller
{

   

    public function __construct(ProductFaq $productFaq)
    {
        $this->productFaq = $productFaq;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $productId)
    {
        //
        return $productFaq = $this->productFaq->where('product_id', $productId)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FaqStoreRequest $request, $productId)
    {
        // return $request->all();
        $newFaq = new $this->productFaq;
        $newFaq->user_id = $request->user_id;
        $newFaq->product_id = $productId;
        $newFaq->title = $request->title;
        $newFaq->description = $request->description;
        
        if ($newFaq->save()) {
            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessCreateProductFaq');
        } else {
            $statusCode = 400;
            $data['message'] = config('app_messages.SomethingWentWrong');
        }
        return Response::json($data, $statusCode);
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
        //mostly the descriptions answer question
        $productFaq = $this->productFaq->where('product_id', $productId)->where('id', $faqId)->first();

        if ($productFaq) {
            $productFaq->description = $request->description;
            $productFaq->save();
            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessUpdateProductFaq');
        } else {
            $statusCode = 400;
            $data['message'] = config('app_messages.SomethingWentWrong');
        }
        return Response::json($data, $statusCode);
    }
}
