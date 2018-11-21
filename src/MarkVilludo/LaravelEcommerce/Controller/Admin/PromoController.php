<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\PromoResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Promo;

use Response;
use Session;
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
        //get data from api
        return view('admin.promo.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.promo.create');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($promoId)
    {
        // return $promoId;
        $data['promo'] = new PromoResource($this->promo->find($promoId));

        return view('admin.promo.edit', $data);
    }
}
