<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Package\PackageStoreRequest;
use App\Http\Resources\PackageResource;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\ProductVariant;
use App\Models\PackageItem;
use Response;
use Validator;
use Config;

class PackageController extends Controller
{

   
    //construction model vairable
    public function __construct(Package $package, PackageItem $packageItem, ProductVariant $productVariant)
    {
        $this->package = $package;
        $this->packageItem = $packageItem;
        $this->productVariant = $productVariant;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packages = $this->package->with('items.product')->with('items.variant')->paginate(10);

        if ($packages) {
            $data = PackageResource::collection($packages);
            return $data;
        } else {
            $data['message'] = config('app_messages.NoPackageAvailable');
            $statusCode = 200;
            return response()->json($data, $statusCode);
        }
    }

    /**
     * Search package
     *
     * @return \Illuminate\Http\Response
    */
    public function searchPackage(Request $request)
    {
        // return $request->all();
        $package = $this->package->getByName($request->search)->paginate(10);

        if ($package) {
            $data = PackageResource::collection($package);
            return $data;
        } else {
            $data['message'] = 'There is no promos available.';
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
    public function store(PackageStoreRequest $request)
    {

        $newPackage = new $this->package;
        $newPackage->name = $request->name;
        $newPackage->description = $request->description;
        $newPackage->status = $request->status;
        $newPackage->price = $request->price;
        
        if ($newPackage->save()) {
            if ($request->products) {
                foreach ($request->products as $key => $product) {
                    //check if exist then avoid duplicate entry
                    // $checkExist = $this->packageItem->where('product_id', $product['product_od'])
                    //                                 ->where('variant_id', $product['variant_id'])
                    //                                 ->first();
                    // if (!$checkExist) {
                        $newPackageItem = new $this->packageItem;
                        $newPackageItem->package_id = $newPackage->package_id;
                        $newPackageItem->product_id = $product['product_id'];
                        $newPackageItem->variant_id = $product['variant_id'];
                        $newPackageItem->quantity = $product['quantity'];
                        $newPackageItem->save();
                    // }
                }
            }
            $data['message'] = config('app_messages.SuccessAddedPackage');
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.SomethingWentWrong');
            $statusCode = 400;
        }
        return Response::json(['data' => $data], $statusCode);
    }

    public function uploadPackageImage(Request $request)
    {
        return $request->all();
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
        $package = $this->package->where('id', $id)->first();
        if ($package) {
             $data['package'] = new PackageResource($this->package->where('id', $id)
                                                                  ->with('items.product.variants')
                                                                  ->with('items.variant')
                                                                  ->first());

            $packageItemArray = [];

            foreach ($package->items as $key => $item) {
                $packageItemArray[] = [
                                    'id' => $item->product['id'],
                                    'name' => $item->product['name'],
                                    'variant' => $item->variant->id,
                                    'variants' => $item->product['variants'],
                                    'quantity' => $item->quantity
                                ];
            }
            $data['packageItem'] = $packageItemArray;

            $statusCode = 200;
            $data['message'] = 'Shows package details.';
        } else {
            $statusCode = 404;
            $data['message'] = 'Package not found.';
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
    public function update(Request $request, $packageId)
    {
        //Validate
        $rules = ['name' => 'required|'.Rule::unique('packages')->ignore($packageId, 'id')];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $data['errors'] = [$validator->errors()];
            $statusCode = 422;
        } else {
            $package = $this->package->find($packageId);

            if ($package && $package->status) {
                $package->name = $request->name;
                $package->description = $request->description;
                $package->status = $request->status;
                $package->price = $request->price;
                
                if ($package->save()) {
                    if ($request->products) {
                        //get all package items
                        $packageItems = $this->packageItem->where('package_id', $packageId)->get();

                        foreach ($packageItems as $key => $packageItem) {
                            //return stocks on variant before remove and create new package items
                            $variant = $this->productVariant->find($packageItem->variant_id);
                            //check if exist
                            if ($variant) {
                                $variant->increment('inventory', $packageItem->quantity);
                            }
                        }
                        //then remove all package existing package items
                        $this->packageItems->find($packageId)->delete();

                        foreach ($request->products as $key => $product) {
                            $newPackageItem = new $this->packageItem;
                            $newPackageItem->package_id = $packageId;
                            $newPackageItem->product_id = $product['product_id'];
                            $newPackageItem->variant_id = $product['variant_id'];
                            $newPackageItem->quantity = $product['quantity'];
                            $newPackageItem->save();

                            //less stocks in product variant
                            $variant = $this->productVariant->find($product['variant_id']);
                            //check if exist
                            if ($variant) {
                                $variant->decrement('inventory', $product['quantity']);
                            }
                        }
                    }
                    $data['message'] = config('app_messages.SuccessUpdatePackage');
                    $statusCode = 200;
                } else {
                    $data['message'] = config('app_messages.SomethingWentWrong');
                    $statusCode = 400;
                }
            } else {
                $data['message'] = config('app_messages.PackageNotFound');
                $statusCode = 400;
            }
        }
        return Response::json(['data' => $data], $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // return $id;
        $package = $this->package->where('id', $id)->withTrashed()->first();

        if ($package) {
            if (!$package->deleted_at) {
                $package->delete();

                //Delete also it's package item
                $packageItem = $this->packageItem->where('id', $id)->delete();

                $data['message'] = config('app_messages.SuccessDeletePackage');
                $statusCode = 200;
            } else {
                $data['message'] = config('app_messages.PackageAlreadyDeleted');
                $statusCode = 400;
            }
        } else {
            $data['message'] = config('app_messages.PackageNotFound');
            $statusCode = 404;
        }
        return Response::json($data, $statusCode);
    }
}
