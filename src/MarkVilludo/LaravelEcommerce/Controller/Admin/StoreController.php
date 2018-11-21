<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Api\Store\StoresStoreRequest;
use App\Http\Resources\StoreResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Store;
use Response;
use Session;

class StoreController extends Controller
{

   
    //construct model variable
    public function __construct(Store $store)
    {
        $this->store = $store;
    }
    /**
     * Display a listing of available stores.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get data from api controller
        return view('admin.store.index');
    }
    /**
     * create store
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.store.create');
    }

     /**
     * Edit details
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($storeId)
    {
        // return $storeId;
        $data['storeId'] = $storeId;

        return view('admin.store.edit', $data);
    }
}
