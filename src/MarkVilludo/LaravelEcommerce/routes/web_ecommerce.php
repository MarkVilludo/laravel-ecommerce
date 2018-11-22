<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*Route::get('/', function () {
    //vue sample crud task
    // return view('welcome');
    return view('welcome_laravel');
})->name('welcome');

*/
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/home', function () {
    //for the mean time we assigned user per each roles but it always possible to assign with multiple roles per user
    if (auth()->user()->hasRole('Admin')) {
        //Admin
        return view('admin.dashboard');
    } else if(auth()->user()->hasRole('Super Admin')){
        return view('superadmin.dashboard');
    } else if(auth()->user()->hasRole('Admin')){
        return view('customer.dashboard');
    } else {
	  	//Customer
        return view('customer.dashboard');
    }
});
Route::get('/logout', function () {
    //logout and flush sessions
    auth()->logout();
    session()->flush();
    return view('login');
})->name('web.logout');

//login
//forgot password send new password (auto generated) via email
Route::get('forgot-password', 'Auth\ForgotPasswordController@forgotPassword');
Route::post('forgot-password', 'Auth\ResetPasswordController@sendEmail')->name('send-email-forgot-password');
Route::post('/login', 'Auth\LoginController@login')->name('web.login');

Route::get('/payment/paymentView', 'Payment\BraintreeController@paymentView')->name('payment.view');
Route::get('/payment/process', 'Payment\BraintreeController@process')->name('payment.process');


//Payment routes
Route::prefix('payment/paypal')->group(function () {
	//payment form
	// Route::get('/', 'Api\Payment\PaypalController@index')->name('api.payment_paypal');
	// route for processing payment
	Route::post('/', 'Api\Payment\PaypalController@payWithpaypal')->name('api.post_payment_paypal');
	// route for check status of the payment
	Route::get('/status', 'Api\Payment\PaypalController@getPaymentStatus');
	//Braintree
	// Route::post('braintree', 'Api\Payment\BraintreeController@postPayment')->name('payment.braintree');
});


// Admin
//Products
Route::group(['middleware' => 'auth'], function() {


	//products
	//super admin route
	include_once('superadmin.php');

 	Route::get('/profile','Admin\UserController@profile')->name('user.profile');

	Route::prefix('products')->group(function () {
		//list of products
	    Route::get('/','Admin\ProductController@index')->name('products.index');
		//View create product fomr
	    Route::get('/create','Admin\ProductController@create')->name('product.create');
		//create
	    // Route::post('/','Admin\ProductController@store')->name('product.store');
	    //edit product
	    Route::get('edit/{id}','Admin\ProductController@edit')->name('product.edit');
		//update
	    Route::post('/{id}','Admin\ProductController@update');
		//delete product
	    Route::delete('/{id}','Admin\ProductController@destroy')->name('product.destroy');

	    //Product Variants
	    //create
		Route::get('/{id}/variants','Admin\ProductVariantController@create')->name('product.variant.create');
		//Add variant in product
		Route::post('/{id}/variants','Admin\ProductVariantController@store');
		//edit
		Route::get('/{id}/variants/{variantId}','Admin\ProductVariantController@edit')->name('product.variant.edit');
		// //Update variant details
		Route::post('/{id}/variants/{variantId}','Admin\ProductVariantController@update')->name('product.variant.update');
		//Delete product variant and its attach product image if any
		Route::get('/{id}/variants/{variantId}/delete','Admin\ProductVariantController@destroy');

		//Product Info
		Route::get('/{id}/info','Admin\ProductInfoController@create')->name('product.info.create');
		//Store
		Route::post('/{id}/info','Admin\ProductInfoController@store');
		//edit
		Route::get('/{id}/info/{infoId}','Admin\ProductInfoController@edit')->name('product.info.edit');
		//Update faq details
		Route::post('/{id}/info/{infoId}','Admin\ProductInfoController@update')->name('product.info.update');
		//Delete product faq
		Route::get('/{id}/info/{infoId}/delete','Admin\ProductInfoController@destroy');

		//Create review cms
		Route::get('/{id}/reviews','Admin\ProductReviewController@create')->name('product.review.create');
		//Api save product reviews
		Route::post('/{id}/reviews','Api\ProductReviewsController@store')->name('api.product_review.save');
		//edit
		Route::get('/{id}/reviews/{reviewId}','Admin\ProductReviewController@edit')->name('product.review.edit');
		//update store details
		Route::post('/{id}/reviews/{reviewId}','Api\ProductReviewsController@update')->name('product.review.update');
		//Delete product review
		Route::get('/{id}/reviews/{reviewId}/delete','Admin\ProductReviewController@destroy');
 	});

	//child sub category
	Route::prefix('child_categories')->group(function () {
		 //Show category details
	    Route::get('/{id?}','Api\ChildSubCategoryController@show')->name('api.sub_category_details');
	});

	Route::prefix('stores')->group(function () {
		//store list
		Route::get('/','Admin\StoreController@index')->name('stores.index');
		//Create ew store
		Route::get('/create','Admin\StoreController@create')->name('store.create');
		//Edit store
		Route::get('/{id}/edit','Admin\StoreController@edit')->name('store.edit');
		//remove store
		Route::delete('/{id}','Admin\StoreController@destroy')->name('store.destroy');

	});

	//Journal
	Route::prefix('journals')->group(function () {
		//journals list
		Route::get('/','Admin\JournalController@index')->name('journals.index');
		//edit journal
		Route::get('/{id}/details','Admin\JournalController@edit')->name('journals.edit');
		//create journal
		Route::get('/create','Admin\JournalController@create')->name('journal.create');
	});

	Route::prefix('journal_categories')->group(function () {
		Route::get('/','Admin\JournalCategoryController@index')->name('journals_category.index');

	});

	//Price range
	Route::prefix('price_range')->group(function () {
		Route::get('/','Admin\PriceRangeController@index')->name('price_range.index');

	});
	

	//Shopping bags
	Route::prefix('shopping_bags')->group(function () {
		//Order status list
		Route::get('/','Admin\ShoppingBagController@index')->name('shopping_bags.index');
	});

	//Customer wishlist
	Route::prefix('wishlists')->group(function () {
		//list
		Route::get('/','Admin\WishlistController@index')->name('wishlists.index');
		//Delete wishlist
		Route::delete('/{id}','Admin\WishlistController@destroy')->name('cms.delete.wishlist');
	});

	//Promos
	Route::prefix('promos')->group(function () {
		//Order status list
		Route::get('/','Admin\PromoController@index')->name('promos.index');
		//Create promos
		Route::get('/create','Admin\PromoController@create')->name('promos.create');
		//Edit promos
		Route::get('/{id}/edit','Admin\PromoController@edit')->name('promos.edit');
		//Delete promo
		Route::delete('/{id}','Api\PromoController@destroy')->name('api.delete.promo');

	});

	//Orders status
	Route::prefix('status')->group(function () {
		//Order status list
		Route::get('/','Admin\OrderStatusController@index')->name('status.index');
		//Create
		Route::get('/create','Admin\OrderStatusController@create')->name('status.create');
		//Add order status
		Route::post('/','Admin\OrderStatusController@store');
		//Edit order status
		Route::get('/{id}','Admin\OrderStatusController@edit')->name('order_status.edit');
		//Update order status
		Route::post('/{id}','Admin\OrderStatusController@update')->name('order_status.update');
		//Delete order status
		Route::delete('/{id}','Admin\OrderStatusController@destroy')->name('order_status.destroy');
	});

	//Order Tags
	Route::prefix('tags')->group(function () {
		//Order tags list
		Route::get('/','Admin\OrderTagsController@index')->name('tags.index');
		//Create
		Route::get('/create','Admin\OrderTagsController@create')->name('tags.create');
		//Add order tag
		Route::post('/','Admin\OrderTagsController@store');
		//Edit order tag
		Route::get('/{id}','Admin\OrderTagsController@edit')->name('tags.edit');
		//Update order tag
		Route::post('/{id}','Admin\OrderTagsController@update')->name('tags.update');
		//Delete order tag
		Route::delete('/{id}','Admin\OrderTagsController@destroy')->name('tags.destroy');
	});

	//Categories
	Route::prefix('categories')->group(function () {
    	//list of child categories
	    Route::get('/','Admin\ChildSubCategoryController@index')->name('categories.index');
		//View product details
	    Route::get('/create','Admin\ChildSubCategoryController@create')->name('category.create');
	    //View product details
	    Route::get('/{id}','Admin\ChildSubCategoryController@show');
		//create
	    Route::post('/','Api\ChildSubCategoryController@store')->name('api.category.store');
	    //edit product
	    Route::get('/{id}/edit','Admin\ChildSubCategoryController@edit')->name('category.edit');
		//update
	    Route::post('/{id}','Api\ChildSubCategoryController@update')->name('api.category.update');
		
 	});

 	//Customers
	Route::prefix('customers')->group(function () {
		//List of customer
		Route::get('/','Admin\CustomerController@index')->name('customers.index');
		//edit customer details
	    Route::get('details/{id}','Admin\CustomerController@show')->name('customer.show');

	    //Get customer order details
	    Route::get('/{id}/orders/{orderId}','Admin\OrderController@customerOrderDetails')->name('customer.order.show');

	    //Create note each order item
	    Route::get('/{id}/orders/{orderId}/item/{ordertemId}/create-note','Admin\OrderController@customerOrderCreateNote')->name('customer.order.note.create');
	    //Store note
     	Route::post('/{id}/orders/{orderId}/store','Admin\OrderController@customerOrderStoreNote')->name('customer.order.note.create');
	});

	//Orders
	Route::prefix('orders')->group(function () {
		//Order list
		Route::get('/','Admin\OrderController@index')->name('orders.index');
		//Order list
		Route::get('/{id}/status/{statusId}','Admin\OrderController@updateStatus')->name('orders.change_status');

		//update status multiple
	 	Route::post('/','Api\OrderController@updateMultipleStatus')->name('update-multiple.orders');

	});
	Route::prefix('order_items')->group(function () {
		//Update order item shipping days
		Route::post('/','Api\OrderController@updateShippingDays')->name('api.update_shipping_days');
	});
    //Vouchers
    Route::prefix('vouchers')->group(function () {
        //Get voucher list
        Route::get('/','Admin\VoucherController@index')->name('vouchers.index');
        //search voucher
        Route::get('/search','Api\VoucherController@searchVoucher')->name('api.search.voucher');

        Route::get('/create','Admin\VoucherController@create')->name('vouchers.create');
        Route::post('/store','Admin\VoucherController@store')->name('vouchers.store');
        Route::get('/edit/{id}','Admin\VoucherController@edit')->name('vouchers.edit');
        Route::get('/details/{id}','Api\VoucherController@show')->name('api.vouchers.details');
        Route::post('/update/{id}','Admin\VoucherController@update')->name('vouchers.update');
        Route::delete('/delete/{id}','Admin\VoucherController@destroy')->name('vouchers.destroy');
    });

    //Frequently bought together
    Route::prefix('fbt')->group(function () {
	 	//list
        Route::get('/','Admin\FBTController@index')->name('fbt.index');
        //create
        Route::get('/create','Admin\FBTController@create')->name('fbt.create');
        //edit
        Route::get('{id}/edit','Admin\FBTController@edit')->name('fbt.edit');
        //store new fbt
        Route::post('/','Api\FBTController@store')->name('fbt.store');
        //get fbt details
	 	Route::get('{id}/','Api\FBTController@show')->name('api.fbt_details');
	 	//update fbt content
        Route::post('{id}','Api\FBTController@update')->name('fbt.update');
        //update fbt content
        Route::delete('{id}','Api\FBTController@destroy')->name('fbt.delete');
    });

    Route::prefix('pages')->group(function () {
    	Route::prefix('banners')->group(function () {
    		Route::get('/', 'Admin\BannerController@index')->name('pages.banner.index');
    		//
    		Route::get('/create', 'Admin\BannerController@create')->name('pages.banner.create');
    		//edit banner
    		Route::get('/edit/{id}', 'Admin\BannerController@edit')->name('pages.banner.edit');
    	});
    	Route::prefix('about')->group(function () {
    		//About index
    		Route::get('/', 'Admin\AppController@aboutIndex')->name('pages.about.index');
    		//update about
    		Route::post('/{id}', 'Api\AppController@updateAboutContent')->name('pages.update_about');
    	});
    	Route::prefix('terms-condition')->group(function () {
    		//About index
    		Route::get('/', 'Admin\AppController@termsConditionIndex')->name('pages.terms-condition.index');
    		//update about
    		Route::post('/{id}', 'Api\AppController@updateTermsConditionContent')->name('pages.update_terms-condition');
    	});
    	Route::prefix('privacy-policy')->group(function () {
    		//About index
    		Route::get('/', 'Admin\AppController@privacyPolicyIndex')->name('pages.privacy-policy.index');
    		//update about
    		Route::post('/{id}', 'Api\AppController@updatePrivacyPolicyContent')->name('pages.update_privacy-policy');
    	});
    	Route::prefix('return-policy')->group(function () {
    		//About index
    		Route::get('/', 'Admin\AppController@returnPolicyIndex')->name('pages.return-policy.index');
    		//update about
    		Route::post('/{id}', 'Api\AppController@updateReturnPolicy')->name('pages.update_return-policy');
    	});
    	Route::prefix('contact-page')->group(function () {
    		//contact page for dynamic
    		Route::get('/', 'Admin\AppController@contactPageIndex')->name('pages.contact-page.index');

    	});
    });
    //
});
//API for products , categories, journals, jornal categories, stores, package, etc
Route::prefix('api/v1')->group(function () {
	
	//about, return policy, privacy policy and terms and contidtion
	Route::prefix('about')->group(function () {
		Route::get('/', 'Api\AppController@about')->name('api.pages.about');
		//delete about photo
		Route::delete('{id}/cover_photo', 'Api\AppController@deleteCoverPhoto')->name('api.pages.delete_cover');
	});
	Route::prefix('contact_care')->group(function () {
		Route::get('/', 'Api\AppController@contactCareDetails')->name('api.pages.customer_care');
		//update customer care
		Route::post('/{id}/update', 'Api\AppController@updateCustomerCare')->name('api.pages.update.customer_care');
	});
	Route::prefix('price_range')->group(function () {
		//get list price range
		Route::get('/', 'Api\PriceRangeController@index')->name('api.price_range');
		//Save new price range
		Route::post('/', 'Api\PriceRangeController@store')->name('api.store.price_range');
		//update price range
		Route::post('/{id}', 'Api\PriceRangeController@update')->name('api.update.price_range');
		//Delete price range
		Route::delete('/{id}', 'Api\PriceRangeController@destroy')->name('api.delete.price_range');
	});
	

	Route::get('/terms-condition', 'Api\AppController@termsCondition')->name('api.terms_condition');
	Route::get('/return-policy', 'Api\AppController@returnPolicy')->name('api.return_policy');
	Route::get('/privacy-policy', 'Api\AppController@privacyPolicy')->name('api.privacy_policy');

	Route::post('/upload_image_global','Api\AppController@uploadImage')->name('api.upload_image.global');

	Route::post('/upload_validated_image_global','Api\AppController@uploadImageValidated')->name('api.upload_image_validate.global');

 	Route::post('/upload_image','Api\ProductController@uploadImage')->name('api.product.upload_image');
	//products
	Route::prefix('products')->group(function () {

	    //Filter products / Search product
	    Route::get('/search','Api\ProductController@searchProduct')->name('api.search.products');
		//Most picks products
	    Route::get('/most_picks','Api\ProductController@mostPickProducts');
     	//New arrivals
    	Route::get('/new_arrivals','Api\ProductController@newArrivals');
	    //list of products
	    Route::get('/','Api\ProductController@index')->name('api.products');
		//View product details
	    Route::get('/{id}','Api\ProductController@show')->name('api.products.details');
		//delete product
	    Route::delete('/{id}','Api\ProductController@destroy')->middleware('auth');

		//Product Faqs
		Route::get('/{id}/faqs','Api\ProductFaqsController@index');
		//Add new product faq
		Route::post('/{id}/faqs','Api\ProductFaqsController@store');

		//Get product reviews used for web
		Route::get('/{id}/reviews','Api\ProductReviewsController@productReviews')->name('api.product.reviews');
	});
 	//category --FS21 use only child sub categories
	Route::prefix('categories')->group(function () {

	  	//list of child subcategory
	    Route::get('/','Api\ChildSubCategoryController@index')->name('api.categories');
	    //search categories
    	Route::get('/search','Api\ChildSubCategoryController@searchCategories')->name('api.search.categories');
	    //Create new child category
     	Route::post('/','Api\ChildSubCategoryController@store');
 	 	//get child sub category details
     	Route::get('/{id}','Api\ChildSubCategoryController@show');
     	 //Update category
	    Route::post('/{id}','Api\ChildSubCategoryController@update');
	    // //Remove categories =softdeletes
	    Route::delete('/{id}','Api\ChildSubCategoryController@destroy');

     	//get available colors
		Route::get('/{id}/available_colors','Api\ProductVariantController@getAvailableColors')->name('api.available_colors');

 	});

   //Get recently views of each customer
    Route::get('customers/{id}/products/recently_views','Api\ProductController@recentlyViews')->name('api.recently_views');
    //Search recently viewed products
    Route::get('customers/{id}/products/recently_views/search','Api\ProductController@searchRecentlyViewed')->name('api.search.recently_views');

    //get recently search customers
    Route::get('customers/{id}/products/recently_search','Api\CustomerRecentSearchController@index');

	Route::get('/variant/{id}','Api\ProductVariantController@getVariantDetails')->name('api.variant_details');
	Route::post('/variant/{id}','Api\ProductVariantController@update')->name('api.update_variant');

	//Delete selected variant image
	Route::delete('/variants/{id}/images/{imageId}','Api\ProductVariantController@removeVariantImage')->name('api.remove.variant_image');


	Route::prefix('/fbt')->group(function () {
		//data from api
     	Route::get('/','Api\FBTController@index')->name('api.fbt');
 		//search fbt
 		Route::get('/search','Api\FBTController@searchFBT')->name('api.search.fbt');
    });

	//Promos
	Route::prefix('promos')->group(function () {
		//get promo list
 		Route::get('/','Api\PromoController@index')->name('api.promos');
 		//search promos
 		Route::get('/search','Api\PromoController@searchPromo')->name('api.search.promo');
 		//get promo details
 		Route::get('/{id}','Api\PromoController@show')->name('api.promo_details');
 		//Store promos
		Route::post('/','Api\PromoController@store')->name('api.promos.store');
		//Update promos
		Route::post('{promoId}/','Api\PromoController@update')->name('api.promos.update');
 		
	});
	//journals
	Route::prefix('journals')->group(function () {
		//List
		Route::get('/','Api\JournalController@index')->name('api.journals');
		//search journals
		Route::get('/search','Api\JournalController@searchJournal')->name('api.search.journal');
		//Add journal
		Route::post('/','Api\JournalController@store')->name('api.journal.store');
		//update journal details
		Route::post('/{id}','Api\JournalController@update')->name('api.journal.update');
		//show details
		Route::get('/{id}','Api\JournalController@show')->name('api.journal.show');
		//show details web view
		Route::get('/{id}/web-view','Api\JournalController@showWebView')->name('api.journal.show-web-view');
		//Delete journal
		Route::delete('/{id}','Api\JournalController@deleteJournal')->name('api.journal.destroy');
	});
	//Journals latest random by 2
	Route::get('/journals_latest','Api\JournalController@getLatestJournal')->name('api.latest_journals');
	//Journal categories
	Route::prefix('journal_categories')->group(function () {
		Route::get('/{id}','Api\JournalController@journalCategory');
		//get journal categories
		Route::get('/','Api\JournalCategoryController@index')->name('api.journal.categories');

		//Add journal categories
		Route::post('/','Api\JournalCategoryController@store')->name('api.store.journal_categories');
		//Update journal category
		Route::post('/{id}','Api\JournalCategoryController@update')->name('api.update.journal_categories');
		//Delete journal categories
		Route::delete('/{id}','Api\JournalCategoryController@destroy')->name('api.delete.journal_category');
	});
	//Journal sliders
	Route::prefix('journal_sliders')->group(function () {
		Route::delete('/{id}','Api\JournalController@removeImageSlider');
	});

	//Banners
	Route::prefix('banners')->group(function () {
		//banners api
		Route::get('/','Api\BannerController@index')->name('api.banners');
		//save banner
    	Route::post('/', 'Api\BannerController@store')->name('api.banners.store');
    	//update banner
    	Route::post('/{id}', 'Api\BannerController@update')->name('api.banners.update');
		//delete banners
		Route::delete('/{id}','Api\BannerController@destroy')->name('api.delete_banner');
		//search banner
 		Route::get('/search','Api\BannerController@searchBanner')->name('api.search.banners');

	});
	//Stores
	Route::prefix('stores')->group(function () {
		//get stores list
 		Route::get('/','Api\StoreController@index')->name('api.stores');
 		//search stores
 		Route::get('/search','Api\StoreController@searchStore')->name('api.search.store');
 		//Store details
		Route::post('/','Api\StoreController@store')->name('api.stores.save');
		//get store details
		Route::get('/{id}','Api\StoreController@show')->name('api.stores.details');
		//update store details
		Route::post('/{id}','Api\StoreController@update')->name('api.stores.update');
		//delete store
		Route::delete('/{id}','Api\StoreController@destroy')->name('api.stores.destroy');
	});
	//Api need authentications
	Route::group(['middleware' => 'auth'], function() {
		Route::prefix('orders')->group(function () {
			//update each order status
   	 	/*	Route::post('/{id}/update','Api\OrderController@update')->name('api.update_status.orders');*/

	 		//update each order status
   	 		Route::post('/{id}/item/{itemId}/status','Admin\OrderController@update');
   	 	});
   	 	//Deactivate account
		Route::post('/deactivate/users/{id}','Api\UserController@deactivateAccount')->name('api.user.deactivate');

		Route::prefix('/notifications')->group(function () {
			Route::get('/','Api\NotificationController@index')->name('api.notifications');
		});
		Route::prefix('products')->group(function () {
	 		//store new product
	    	Route::post('/','Api\ProductController@store')->name('api.store_product');
		});
	});

	Route::get('/generate_order_number','Api\OrderController@getOrderNumber')->name('api.generate_number');
	//

});


Route::get('/{vue_capture?}', function () {
	return view('app.index');
})->where('vue_capture', '[\/\w\.-]*');
