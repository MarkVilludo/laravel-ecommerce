<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\RecentlySearchResource;
use App\Models\ProductRecentlySearch;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerRecentSearchController extends Controller
{
    //


    public function __construct(ProductRecentlySearch $customerRecentlySearch)
    {
        $this->customerRecentlySearch = $customerRecentlySearch;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($userId)
    {
        $customerRecentlySearch = $this->customerRecentlySearch->where('user_id', $userId)
                                                        ->orderBy('created_at', 'desc')
                                                        ->paginate(10);
              
            
        if ($customerRecentlySearch) {
            $data = RecentlySearchResource::collection($customerRecentlySearch);
            return $data;
        } else {
            $data['message'] = config('app_messages.ThereIsNoDataAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }
}
