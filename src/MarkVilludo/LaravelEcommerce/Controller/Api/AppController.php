<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Pages\CustomerCareStoreRequest;
use App\Http\Requests\Api\Pages\ContentUpdateRequest;
use App\Http\Requests\Api\Image\ImageStoreRequest;
use App\Http\Resources\TermsConditionResource;
use App\Http\Resources\PrivacyPolicyResource;
use App\Http\Resources\ReturnPolicyResource;
use App\Http\Resources\ContactCareResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\AboutResource;
use App\Http\Controllers\Controller;
use App\Models\TermsCondition;
use App\Models\PrivacyPolicy;
use App\Models\JournalSlider;
use Illuminate\Http\Request;
use App\Models\ReturnPolicy;
use App\Models\ContactCare;
use App\Models\About;
use App\Helpers\Helper;

use Illuminate\Validation\Rule;
use Validator;
use Storage;

class AppController extends Controller
{
    public function __construct(
        PrivacyPolicy $privacyPolicy,
        ReturnPolicy $returnPolicy,
        TermsCondition $termsCondition,
        ContactCare $contactCare,
        About $about,
        JournalSlider $journalSlider
    ) {
        $this->privacyPolicy = $privacyPolicy;
        $this->returnPolicy = $returnPolicy;
        $this->termsCondition = $termsCondition;
        $this->contactCare = $contactCare;
        $this->about = $about;
        $this->journalSlider = $journalSlider;
    }

    //about
    public function about()
    {

        if (Cache::has('about')) {
            $about = Cache::get('about');
        } else {
            $about = Cache::remember('about', config('cache.cacheTime'), function () {
                return $this->about->all();
            });
        }
        return AboutResource::collection($about);
    }

    //terms conditions
    public function termsCondition()
    {

        if (Cache::has('termsCondition')) {
            $termsCondition = Cache::get('termsCondition');
        } else {
            $termsCondition = Cache::remember('termsCondition', config('cache.cacheTime'), function () {
                return $this->termsCondition->all();
            });
        }
        return TermsConditionResource::collection($termsCondition);
    }

    //return policy
    public function returnPolicy()
    {
        if (Cache::has('returnPolicy')) {
            $returnPolicy = Cache::get('returnPolicy');
        } else {
            $returnPolicy = Cache::remember('returnPolicy', config('cache.cacheTime'), function () {
                return $this->returnPolicy->all();
            });
        }

        return ReturnPolicyResource::collection($returnPolicy);
    }

    //privacy policy
    public function privacyPolicy()
    {
        if (Cache::has('privacyPolicy')) {
            $privacyPolicy = Cache::get('privacyPolicy');
        } else {
            $privacyPolicy = Cache::remember('privacyPolicy', config('cache.cacheTime'), function () {
                return $this->privacyPolicy->all();
            });
        }
        return ReturnPolicyResource::collection($privacyPolicy);
    }

    //contact care
    public function contactCareDetails()
    {
        if (Cache::has('contactCare')) {
            $contactCare = Cache::get('contactCare');
        } else {
            $contactCare = Cache::remember('contactCare', config('cache.cacheTime'), function () {
                return $this->contactCare->all();
            });
        }
        return ContactCareResource::collection($contactCare);
    }

    //upload image
    public function uploadImage(Request $request)
    {
        //check product image exist
        if ($request->file('file')) {
            //call resize and crop images function
                $file = $request->file('file');
                $origFilePath = $request->savePath;
                $filename = md5($file->getClientOriginalName());
                $filetype = $file->getClientOriginalExtension();
                Helper::storeImages($file, $origFilePath);
            //end
            if ($request->removePath && $request->file_name) {
                //remove existing image in storage
                //sizes
                $medium = "/medium/";
                $small = "/small/";
                $xsmall = "/xsmall/";

                Storage::delete($request->removePath.$request->file_name);
                Storage::delete($request->removePath.$medium.$request->file_name);
                Storage::delete($request->removePath.$small.$request->file_name);
                Storage::delete($request->removePath.$xsmall.$request->file_name);
            }
            $data['path'] = $origFilePath;
            $data['file_name'] = $filename.'.'.$filetype;

            //for journal check if saved additional images for slider
            if ($request->store_journal) {
                $journalSlider = new $this->journalSlider;
                $journalSlider->journal_id = $request->journal_id;
                $journalSlider->file_name = $filename.'.'.$filetype;
                $journalSlider->path = $origFilePath;
                $journalSlider->save();
            }

            return $data;
        }
    }
    //upload image with size validations
    public function uploadImageValidated(Request $request)
    {

        $rules = [
            'file' => 'required|'.Rule::dimensions()->minWidth(565)->minHeight(376)->maxWidth(1024)->maxHeight(683),
        ];

         $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $data['message'] = json_encode([$validator->errors()]);
            $statusCode = 422;
        } else {
            // return $request->all();
            //check product image exist
            if ($request->file('file')) {
                //call resize and crop images function
                    $file = $request->file('file');
                    $origFilePath = $request->savePath;
                    $filename = md5($file->getClientOriginalName());
                    $filetype = $file->getClientOriginalExtension();
                    Helper::storeImages($file, $origFilePath);
                //end
                    
                $data['path'] = $origFilePath;
                $data['file_name'] = $filename.'.'.$filetype;

                $statusCode = 200;
            }
        }

        return response()->json($data, $statusCode);
    }

    /**
     * Update about page content
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAboutContent(ContentUpdateRequest $request, $id)
    {
        // return $request->all();
        $about = $this->about::find($id);

        if ($about) {
            $about->content = $request->content;
            $about->content_web = $request->content_web;
            $about->file_name = $request->file_name != $about->file_name ? $request->file_name : $about->file_name;
            $about->path = 'storage/about';
            
            if ($about->update()) {
                $statusCode = 200;
                $data['message'] = config('app_messages.SuccessUpdatedAboutPage');
            } else {
                $statusCode = 400;
                $data['message'] = config('app_messages.SomethingWentWrong');
            }
        } else {
            $about = new $this->about;
            $about->content = $request->content;
            $about->file_name = $request->file_name;
            $about->path = $request->file_name;
            $about->save();

            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessUpdatedAboutPage');
        }
        if (Cache::has('about')) {
            //clear cache products
            Cache::forget('about');
            //end clear cache
        }
        
        return response()->json($data, $statusCode);
    }

     /**
     * Update customer care content
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function updateCustomerCare(CustomerCareStoreRequest $request, $id)
    {
        // return $request->all();
        $contactCare = $this->contactCare::find($id);

        if ($contactCare) {
            $contactCare->contact_number = $request->contact_number;
            $contactCare->email = $request->email;
            $contactCare->shipping_concern_email = $request->shipping_concern_email;
            $contactCare->pr_media_inquiry_email = $request->pr_media_inquiry_email;
            $contactCare->partnership_business_inquery_email = $request->partnership_business_inquery_email;

            if ($contactCare->update()) {
                $statusCode = 200;
                $data['message'] = config('app_messages.SuccessUpdatedContactPage');
            } else {
                $statusCode = 400;
                $data['message'] = config('app_messages.SomethingWentWrong');
            }
        } else {
            $contactCare = new $this->contactCare;
            $contactCare->contact_number = $request->contact_number;
            $contactCare->email = $request->email;
            $contactCare->save();

            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessUpdatedContactPage');
        }
        
        if (Cache::has('contactCare')) {
            //clear cache
            Cache::forget('contactCare');
            //end clear cache
        }

        return response()->json($data, $statusCode);
    }

    /**
     * Delete cover photo about
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteCoverPhoto($id)
    {
        // return $id;
        $about = $this->about::find($id);
        //remove existing image in storage
        
        if ($about) {
            //sizes
            $medium = "/medium/";
            $small = "/small/";
            $xsmall = "/xsmall/";
            Storage::delete('public/about'.'/'.$about->file_name);
            Storage::delete('public/about'.$medium.$about->file_name);
            Storage::delete('public/about'.$small.$about->file_name);
            Storage::delete('public/about'.$xsmall.$about->file_name);

            $about->file_name = '';
            $about->path = '';
            $about->update();

            $statusCode = 200;
            $data['message'] =  config('app_messages.SucessRemovedCoverPhotoAboutPage');
        } else {
            $statusCode = 400;
            $data['message'] = config('app_messages.SomethingWentWrong');
        }
        return response()->json($data, $statusCode);
    }


    /**
     * Update terms condition content
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateTermsConditionContent(ContentUpdateRequest $request, $id)
    {
        //
        // return $request->all();
        $termsCondition = $this->termsCondition::find($id);

        if ($termsCondition) {
            $termsCondition->content = $request->content;
            
            if ($termsCondition->update()) {
                $statusCode = 200;
                $data['message'] = config('app_messages.SuccessUpdatedTermsConditions');
            } else {
                $statusCode = 400;
                $data['message'] = config('app_messages.SomethingWentWrong');
            }
        } else {
            $termsCondition = new $this->termsCondition;
            $termsCondition->content = $request->content;
            $termsCondition->save();
            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessUpdatedTermsConditions');
        }
        if (Cache::has('termsCondition')) {
            //clear cache
            Cache::forget('termsCondition');
            //end clear cache
        }
        
        return response()->json($data, $statusCode);
    }

    /**
     * Update privacy policy
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePrivacyPolicyContent(ContentUpdateRequest $request, $id)
    {
        // return $request->all();
        $privacyPolicy = $this->privacyPolicy::find($id);

        if ($privacyPolicy) {
            $privacyPolicy->content = $request->content;
            
            if ($privacyPolicy->update()) {
                $statusCode = 200;
                $data['message'] = config('app_messages.SuccessUpdatedPrivacyPolicyPage');
            } else {
                $statusCode = 400;
                $data['message'] = config('app_messages.SomethingWentWrong');
            }
        } else {
            $privacyPolicy = new $this->privacyPolicy;
            $privacyPolicy->content = $request->content;
            $privacyPolicy->save();
            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessUpdatedPrivacyPolicyPage');
        }

        if (Cache::has('privacyPolicy')) {
            //clear cache
            Cache::forget('privacyPolicy');
            //end clear cache
        }
        
        return response()->json($data, $statusCode);
    }

     /**
     * Update return policy
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateReturnPolicy(ContentUpdateRequest $request, $id)
    {
        // return $request->all();
        $returnPolicy = $this->returnPolicy::find($id);

        if ($returnPolicy) {
            $returnPolicy->content = $request->content;
            
            if ($returnPolicy->update()) {
                $statusCode = 200;
                $data['message'] = config('app_messages.SuccessUpdatedReturnPolicyPage');
            } else {
                $statusCode = 400;
                $data['message'] = config('app_messages.SomethingWentWrong');
            }
        } else {
            $returnPolicy = new $this->returnPolicy;
            $returnPolicy->content = $request->content;
            $returnPolicy->save();
            $statusCode = 200;
            $data['message'] = config('app_messages.SuccessUpdatedReturnPolicyPage');
        }
        if (Cache::has('returnPolicy')) {
            //clear cache
            Cache::forget('returnPolicy');
            //end clear cache
        }
              
        return response()->json($data, $statusCode);
    }
}
