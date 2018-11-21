<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Pages\BannerStoreRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Models\Banner;

class BannerController extends Controller
{

      

    public function __construct(Banner $banner)
    {
        $this->banner = $banner;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banners = $this->banner->orderBy('created_at', 'asc')->paginate(4);

        if ($banners) {
            $data['message'] =  config('app_messages.ShowsBannerList');
            return $data = BannerResource::collection($banners);
        } else {
            $data['message'] =  config('app_messages.NotFoundBanner');
            $statusCode = 200;
        }
        return $data;
    }
    /**
     * Search banner
     *
     * @return \Illuminate\Http\Response
    */
    public function searchBanner(Request $request)
    {
        // return $request->all();
        $banners = $this->banner->getByTitle($request->title)->paginate(10);

        if ($banners) {
            $data = BannerResource::collection($banners);
            return $data;
        } else {
            $data['message'] = config('app_messages.ThereIsNoBannersAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BannerStoreRequest $request)
    {
        $banner = new $this->banner;
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->file_name = $request->image;
        $banner->path = $request->path;

        if ($banner->save()) {
            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessCreatedBanner');
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
    public function update(Request $request, $bannerId)
    {
        // return $request->all();
        $banner = $this->banner->find($bannerId);
        $banner->title = $request->title;
        $banner->description = $request->description;

        if ($request->image) {
            $banner->file_name = $request->image;
            $banner->path = $request->path;
        }
        
        if ($banner->update()) {
            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessUpdatedBanner');
        } else {
            $statusCode = 400;
            $data['message'] = config('app_messages.SomethingWentWrong');
        }

        return response()->json($data, $statusCode);
    }
    /**
     * Remove banner
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($bannerId)
    {
        // return $bannerId;
        $banner = $this->banner->find($bannerId);
        if ($banner) {
            if ($banner->delete()) {
                $data['message'] = config('app_messages.SuccessDeletedBanner');
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.SomethingWentWrong');
                $statusCode = 400;
            }
        } else {
            $data['message'] = config('app_messages.NotFoundBanner');
            $statusCode = 404;
        }
        return response()->json($data, $statusCode);
    }
}
