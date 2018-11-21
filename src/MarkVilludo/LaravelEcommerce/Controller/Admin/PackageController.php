<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Api\Package\PackageStoreRequest;
use App\Http\Requests\Api\Package\PackageUpdateRequest;
use App\Http\Resources\PackageResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\SubCategory;
use App\Models\PackageItem;
use App\Models\Package;
use Response;
use Session;

class PackageController extends Controller
{

   
    /**
     * construct model variable
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(
        Package $package,
        PackageItem $packageItem,
        SubCategory $subCategory,
        ProductVariant $variant
    ) {
        $this->package = $package;
        $this->packageItem = $packageItem;
        $this->subCategory = $subCategory;
        $this->variant = $variant;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['packages'] = PackageResource::collection($this->package->with('items.variant')
                                            ->with('items.product')
                                            ->paginate(10));

        return view('admin.package.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $subCategories = $this->subCategory->where('id', config('setting.FSSubCategory'))
                                           ->with('childSubCategories')
                                           ->first();

        $data['categories'] = $subCategories->childSubCategories;

        return view('admin.package.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PackageStoreRequest $request)
    {
        // return $request->all();
        //generated file name
        $newPackage = new $this->package;
        $newPackage->name = $request->name;
        $newPackage->description = $request->description;
        $newPackage->warranty = $request->warranty;
        $newPackage->warranty_type = $request->warranty_type;
        $newPackage->price = $request->price;
        $newPackage->sub_category_id = config('setting.FSSubCategory');
        $newPackage->child_sub_category_id = $request->child_sub_category_id;
        $newPackage->status = $request->status;
            
         //store package items
        $packageItems = $request->package_items;

        if ($newPackage->save()) {
            if ($packageItems) {
                foreach ($packageItems as $key => $item) {
                    $newPackageItem = new $this->packageItem;
                    $newPackageItem->package_id = $newPackage->id;
                    $newPackageItem->product_id = $item['id'];
                    $newPackageItem->variant_id = $item['variant'];
                    $newPackageItem->quantity = $item['quantity'];
                    $newPackageItem->save();

                    //get variant details by id and decrease its inventory number
                    //check if exist
                    $variant = $this->variant->find($item['variant']);

                    if ($variant) {
                        $variant->decrement('inventory', $item['quantity']);
                    }
                }
            }
            $data['message'] = config('app_messages.SuccessAddedPackage');
            $data['success'] = true;
            $statusCode = 200;
        } else {
            $data['message'] = config('app_messages.SomethingWentWrong');
            $data['success'] = false;
            $statusCode = 400;
        }
        return Response::json($data, $statusCode);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $data['packageId'] = $id;
        $subCategories = $this->subCategory->where('id', config('setting.FSSubCategory'))
                                           ->with('childSubCategories')
                                           ->first();

        $data['categories'] = $subCategories->childSubCategories;
        
        return view('admin.package.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PackageUpdateRequest $request, $packageId)
    {
        // return $request->all();
        $package = $this->package->find($packageId);

        if ($package) {
            $package->name = $request->name;
            $package->description = $request->description;
            $package->warranty = $request->warranty;
            $package->warranty_type = $request->warranty_type;
            $package->price = $request->price;
            $package->sub_category_id = config('setting.FSSubCategory');
            $package->child_sub_category_id = $request->child_sub_category_id;
            $package->status = $request->status;
                    
            if ($package->update()) {
                //store package items
                $packageItems = $request->package_items;
                if ($packageItems) {
                    //remove all product items in package before insert package item collections
                    //but get first the existing product variant and return stocks quantity

                    $existingPackageItems = $this->packageItem->getItems($packageId)->get();
                    foreach ($existingPackageItems as $key => $existingPackageItem) {
                        $variant = $this->variant->find($existingPackageItem->variant_id);
                        if ($variant) {
                            $variant->increment('inventory', $existingPackageItem->quantity);
                        }
                    }
                    //Delete all package items after return all product variant stock(s)
                    $deleteAllExistingItems = $this->packageItem->getItems($packageId)->delete();

                    if ($packageItems) {
                        foreach ($packageItems as $key => $item) {
                            $newPackageItem = new $this->packageItem;
                            $newPackageItem->package_id = $packageId;
                            $newPackageItem->product_id = $item['id'];
                            $newPackageItem->variant_id = $item['variant'];
                            $newPackageItem->quantity = $item['quantity'];
                            $newPackageItem->save();

                            //get variant details by id and decrease its inventory number
                            //check if exist
                            $variant = $this->variant->find($item['variant']);

                            if ($variant) {
                                $variant->decrement('inventory', $item['quantity']);
                            }
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
        return Response::json($data, $statusCode);
    }

    /**
     * Remove package
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($packageId)
    {
        // return $packageId;

        $package = $this->package->where('id', $packageId)->withTrashed()->first();

        if ($package) {
            if (!$package->deleted_at) {
                $package->delete();

                //Delete also it's package item
                $packageItem = $this->packageItem->where('package_id', $packageId)->delete();

                $message = config('app_messages.SuccessDeletePackage');
                $statusCode = 200;
            } else {
                $message = config('app_messages.PackageAlreadyDeleted');
                $statusCode = 400;
            }
        } else {
            $message = config('app_messages.PackageNotFound');
            $statusCode = 404;
        }
        
        Session::flash('message', $message);
        return redirect()->back();
    }
}
