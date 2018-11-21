<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Store\StoresUpdateRequest;
use App\Http\Requests\Api\Store\StoresStoreRequest;
use App\Http\Resources\StoreResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Store;

use Validator;
use Response;

class StoreController extends Controller
{
   
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

        if (Cache::has('store'.request('page'))) {
            $store = Cache::get('store'.request('page'));
        } else {
            $store = Cache::remember('store'.request('page'), config('cache.cacheTime'), function () {
                return $this->store->orderBy('name', 'asc')->paginate(10);
            });
        }

        if ($store) {
            $data = StoreResource::collection($store);
            return $data;
        } else {
            $data['message'] = 'There is no store available.';
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }
    /**
     * Search store
     *
     * @return \Illuminate\Http\Response
    */
    public function searchStore(Request $request)
    {
        // return $request->title;
        $stores = $this->store->getByName($request->name)
                              ->paginate(10);

        if ($stores) {
            $data = StoreResource::collection($stores);
            return $data;
        } else {
            $data['message'] = 'There is no store available.';
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }
    /**
     * Store details
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoresStoreRequest $request)
    {
        //clear cache stores when create new
        Cache::forget('store');
        //end
        // return $request->all();
        $newStore = new $this->store;
        $newStore->name = $request->name;
        $newStore->complete_address = $request->complete_address;
        
        if ($newStore->save()) {
            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessCreatedStore');
        } else {
            $statusCode = 400;
            $data['message'] = config('app_messages.SomethingWentWrong');
        }

        return response()->json($data, $statusCode);
    }
    /**
     * Show store details
     *
     * @return \Illuminate\Http\Response
     */
    public function show($storeId)
    {
        // return $request->all();
        $data['store'] = $this->store->find($storeId);
        
        if ($data['store']) {
            $statusCode = 200;
            $data['message'] = 'Shows store details.';
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.NotFoundStore');
        }

        return response()->json($data, $statusCode);
    }

    /**
     * Update store details
     *
     * @return \Illuminate\Http\Response
     */
    public function update(StoresUpdateRequest $request, $storeId)
    {
        // return $storeId;

        $store = $this->store->find($storeId);
        $store->name = $request->name;
        $store->complete_address = $request->complete_address;
        
        if ($store->update()) {
            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessUpdatedStore');
        } else {
            $statusCode = 400;
            $data['message'] = config('app_messages.SomethingWentWrong');
        }

        return response()->json($data, $statusCode);
    }
    /**
     * Remove store
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($storeId)
    {
        //clear cache stores when create new
        Cache::forget('store');
        $store = $this->store->find($storeId);

        if ($store) {
            $store->delete();
            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessDeletedStore');
        } else {
            $statusCode = 404;
            $data['message'] = config('app_messages.NotFoundStore');
        }
        return response()->json($data, $statusCode);
    }
}
