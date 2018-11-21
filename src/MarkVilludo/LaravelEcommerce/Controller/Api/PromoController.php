<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Promo\PromoUpdateRequest;
use App\Http\Requests\Api\Promo\PromoStoreRequest;
use App\Http\Resources\PromoResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Promo;
use Validator;
use Response;
use Storage;
use File;

class PromoController extends Controller
{
    //construct model variable
    public function __construct(Promo $promo)
    {
        $this->promo = $promo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $promos =  $this->promo->paginate(10);

        if ($promos) {
            $data = PromoResource::collection($promos);
            return $data;
        } else {
            $data['message'] = 'There is no promos available.';
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }
    /**
     * Display the specified promo details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $promo = $this->promo->find($id);
        
        if ($promo) {
            $data['message'] = config('app_messages.ShowPromoDetails');
            $promoDetails = new PromoResource($promo);

            $data['promo'] = $promoDetails;

            return $data;
        } else {
            $data['message'] = config('app_messages.NotFoundPromo');
            $statusCode = 404;
            return response()->json($data, $statusCode);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PromoStoreRequest $request)
    {

        $promo = new $this->promo;
        $promo->name = $request->name;
        $promo->description = $request->description;
        $promo->start_date = $request->start_date;
        $promo->end_date = $request->end_date;
        $promo->file_name = $request->image;
        $promo->path = $request->path;
        $promo->status = $request->status;
        
        if ($promo->save()) {
            $statusCode = 200;
            $data['message'] = config('app_messages.SucessCreatedPromo');


            //Make notifications to users for new promos
            Helper::makeNotificationsToCustomer($promo->id, $promo->name, $promo->description);
            //end notify users
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
    public function update(PromoUpdateRequest $request, $promoId)
    {
        
        
        $promo = $this->promo->find($promoId);
        $promo->name = $request->name;
        $promo->description = $request->description;
        if ($request->image) {
            $promo->file_name = $request->image ? $request->image : $promo->image;
            $promo->path = $request->path ? $request->path : $promo->path;
        }
        $promo->start_date = $request->start_date;
        $promo->end_date = $request->end_date;
        $promo->status = $request->status;
        
        if ($promo->update()) {
            $statusCode = 200;
            $data['message'] = config('app_messages.SucessUpdatedPromo');
        } else {
            $statusCode = 400;
            $data['message'] = config('app_messages.SomethingWentWrong');
        }
        return response()->json($data, $statusCode);
    }
    /**
     * Search promo
     *
     * @return \Illuminate\Http\Response
    */
    public function searchPromo(Request $request)
    {
        // return $request->all();
        $promos = $this->promo->getByName($request->search)->paginate(10);

        if ($promos) {
            $data = PromoResource::collection($promos);
            return $data;
        } else {
            $data['message'] = 'There is no promos available.';
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }
    /**
     * Remove promo
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($promoId)
    {
        // return $promoId;

        $promo = $this->promo->find($promoId);
        if ($promo) {
            if ($promo->delete()) {
                $data['message'] = config('app_messages.SucessDeletedPromo');
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.SomethingWentWrong');
                $statusCode = 400;
            }
        } else {
            $data['message'] = config('app_messages.NotFoundPromo');
            $statusCode = 404;
        }
        return response()->json($data, $statusCode);
    }
}
