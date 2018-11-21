<?php


use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//test routes
Route::resource('v1/tasks', 'SuperAdmin\TaskController',[
	'except' => ['create','edit','show']
]);
Route::post('/v1/login', 'Api\PassportController@login')->name('login');
Route::post('/v1/register', 'Api\PassportController@register')->name('register');
//subscribe email
Route::post('/v1/subscribe', 'Api\SubscriptionController@sendVerification')->name('send-email-verification');
Route::get('/v1/subscription-confirm/{key}', 'Api\SubscriptionController@verifySubscription')->name('api.verified-subscription');

//forgot password
Route::post('/v1/forgot-password', 'Auth\ResetPasswordController@sendEmailApi')->name('send-email-forgot-password');

Route::post('/v1/logout','Api\PassportController@logout')->name('logout');

//Payment routes
// Route::prefix('paypal')->group(function () {
// 	//payment form
// 	Route::get('/', 'Api\Payment\PaypalController@index')->name('api.payment_paypal');
// 	// route for processing payment
// 	Route::post('/', 'Api\Payment\PaypalController@payWithpaypal')->name('api.post_payment_paypal');
// 	// route for check status of the payment
// 	Route::get('/status', 'Api\Payment\PaypalController@getPaymentStatus');
// 	//Braintree
// 	// Route::post('braintree', 'Api\Payment\BraintreeController@postPayment')->name('payment.braintree');
// });

Route::group(['middleware' => 'auth:api', 'throttle:rate_limit,1'], function(){
	//group by v1
	
	Route::prefix('v1')->group(function () {

		//Validate user orders before to proceed to checkout
		Route::get('/validate-orders','Api\ShoppingBagController@validateOrders');

		//checkout orders
		Route::post('/checkout','Api\ShoppingBagController@checkout');

		//get summary of data for dashboard
		Route::get('/dashboard','Api\DashboardController@index')->name('api.dashboard');
		//shopping bags
		Route::prefix('shopping_bags')->group(function () {
			//get live item added in shopping bags
			Route::get('/','Api\ShoppingBagController@getLiveShoppingBags')->name('api.shopping_bags');
			//search 
			Route::post('/search','Api\ShoppingBagController@searchShoppingBags')->name('api.search.shopping_bags');
		});
		//Wishlist
		Route::prefix('wishlists')->group(function () {
			//get live item added in shopping bags
			Route::get('/','Api\WishlistController@getLiveWishlists')->name('api.wishlist');
			//search 
			// Route::post('/search','Api\WishlistController@searchWishlist')->name('api.search.wishlist');
		});

		Route::prefix('permissions')->group(function () {
			//All permissions
			Route::get('/', 'Api\PermissionController@index');
			//Create new permission
			Route::post('/', 'Api\PermissionController@store');
			//Update permission
			Route::post('/{id}', 'Api\PermissionController@update');
			//Delete permission
			Route::delete('/{id}', 'Api\PermissionController@destroy');
		});
		
		Route::prefix('roles')->group(function () {
			//All roles
			Route::get('/', 'Api\RoleController@index');
			//Create new role
			Route::post('/', 'Api\RoleController@store');
			//Update role
			Route::post('/{id}', 'Api\RoleController@update');
			//Delete role
			Route::delete('/{id}', 'Api\RoleController@destroy');
		});

		Route::prefix('packages')->group(function(){
			//Package list
			Route::get('/','Api\PackageController@index')->name('api.packages');
			//search packages
   	 		Route::post('/search','Api\PackageController@searchPackage')->name('api.search.package');
			//Store new package
			Route::post('/','Api\PackageController@store');
			//Update package details
			Route::post('/{id}','Api\PackageController@update');
			//Upload package images
			Route::post('/{id}/images','Api\PackageController@uploadPackageImage');

			//Get package details
			Route::get('/{id}','Api\PackageController@show');
			
		});
		//products
		Route::prefix('products')->group(function () {
			//create
		    Route::post('/','Api\ProductController@store')->name('api.store_product');

			//update
		    Route::post('/{id}','Api\ProductController@update');
		    
		    //Product Image
 			Route::get('/{id}/images','Api\ProductImageController@index');
 			//Create new image attach in product
 			Route::post('/{id}/images','Api\ProductImageController@store');
 			//Delete image from product
 			Route::delete('/{id}/images/{productImageId}','Api\ProductImageController@destroy');

		    //Product Variants
 			Route::get('/{id}/variants','Api\ProductVariantController@index');
 			//Add variant in product
 			Route::post('/{id}/variants','Api\ProductVariantController@store')->name('api.add_variant');
 			//Update variant details
 			Route::post('/{id}/variants/{variantId}','Api\ProductVariantController@update');
 			//Delete product variant and its attach product image if any
 			Route::delete('/{id}/variants/{variantId}','Api\ProductVariantController@destroy');

 			//Save variant image 
 			Route::post('/{id}/variants/{variantId}/images','Api\ProductImageController@storeVariantImage');

 			//Product Faqs
 			Route::get('/{id}/faqs','Api\ProductFaqsController@index');
 			//Add new product faq
 			Route::post('/{id}/faqs','Api\ProductFaqsController@store');
 			//Update or answer product faq questions.
 			Route::post('/{id}/faqs/{faqId}','Api\ProductFaqsController@update');

	 	});

	 	//check of existing in bags and in wishlists
	 	Route::get('/check_bags_wishlists','Api\ProductController@checkExistBagsAndWishlist');
	 
		//Users
		Route::prefix('users')->group(function () {
			//user list
		  	Route::get('/','Api\UserController@index');
			//Get user details
			// Route::get('/{id}','Api\UserController@profile');
			//create user
			Route::post('/', 'Api\UserController@store');
			//Update user details
			Route::post('/{id}/update','Api\UserController@update')->name('api.update.profile');
		});
		
		//Get user profile
		Route::get('/profile','Api\UserController@profile')->name('api.user.profile');

		//Customers
		Route::prefix('customers')->group(function () {

			Route::get('/list','Api\UserController@getCustomers')->name('api.customers');
		
			//Search
			Route::get('/search','Api\UserController@searchCustomer')->name('api.search.customer');

			//Get all user details
   	 		Route::get('/{id}/accounts','Api\UserController@getUserAccountDetails')->name('api.customer.details');
   	 		Route::get('/{id}/orders','Api\OrderController@myOrders')->name('api.customer_orders');
   	 		Route::get('/{id}/orders_cms','Api\OrderController@customerOrdersCMS')->name('api.customer_orders_cms');
   	 		Route::get('/{id}/orders/{orderId}','Api\OrderController@getCustomerOrders');

			//Shopping bag 	
			//Get all bag items
		    Route::get('/{id}/bags','Api\ShoppingBagController@index');
			//Add item in cart
			Route::post('/{id}/bags/','Api\ShoppingBagController@store');

			//Add multiple item in cart
			Route::post('/{id}/bags-multiple','Api\ShoppingBagController@storeMultipleItem');
			//Update item quantity in bag
			Route::post('/{userId}/bags/{id}','Api\ShoppingBagController@update');

			//Move cart item in wishlist
			Route::post('/{userId}/bags/{id}/move-wishlists','Api\ShoppingBagController@moveItemToWishList');
			//Delete item in bag
			Route::delete('/{userId}/bags/{id}','Api\ShoppingBagController@destroy');

   	 		//Wishlist
   	 		Route::get('/{id}/wishlists','Api\WishlistController@index');
   	 		//Add product to wishlist
   	 		Route::post('/{id}/wishlists','Api\WishlistController@store');
   	 		//Add multiple product to wishlists
   	 		Route::post('/{id}/wishlists-multiple','Api\WishlistController@storeMultipleProduct');
   	 		//Update wishlist
   	 		Route::post('/{id}/wishlists/{wishListId}','Api\WishlistController@update');
   	 		//Move product to shopping bag
   	 		Route::post('/{id}/wishlists/{wishListId}/move-bags','Api\WishlistController@moveToShoppingBag');
   	 		//Remove product from wishlist
   	 		Route::delete('/{id}/wishlists/{wishListId}','Api\WishlistController@destroy');

   	 		//address
   	 		Route::get('/{id}/address','Api\CustomerAddressController@index');
   	 		//Add address
   	 		Route::post('/{id}/address','Api\CustomerAddressController@store');
   	 		//Update billing address
   	 		Route::post('/{id}/address/{addressId}','Api\CustomerAddressController@update');
   	 		//Set default used address 
   	 		Route::post('/{id}/address/{addressId}/default','Api\CustomerAddressController@setDefaultAddress');
   	 		//Remove billing address
   	 		Route::delete('/{id}/address/{addressId}','Api\CustomerAddressController@destroy');
   	 	
	 		//Update user notifications
			Route::post('/{id}/notifications','Api\UserController@notification');


			//List of customer reviews
			Route::get('/{id}/reviews','Api\CustomerReviewController@index');
			//Create new reviews
			Route::post('/{id}/reviews','Api\CustomerReviewController@store');
			//get review deta
			Route::get('/{id}/reviews/{reviewId}','Api\CustomerReviewController@show');
			//update reviews
			Route::post('/{id}/reviews/{reviewId}','Api\CustomerReviewController@update');
			//delete reviews
			Route::delete('/{id}/reviews/{reviewId}','Api\CustomerReviewController@destroy')->name('product.review.delete');

			
		});

		Route::prefix('orders')->group(function () {
			//get order list
   	 		Route::get('/','Api\OrderController@index')->name('api.orders');
   	 		//Search orders
   	 		Route::get('/search','Api\OrderController@searchOrders')->name('api.search.orders');
   	 		//Order details
   	 		Route::get('/{id}','Api\OrderController@show')->name('api.order_details');

   	 		//update each order status
   	 		Route::post('/{id}/cancel_orders','Api\OrderController@update');
   	 	
		});
		//Orders status
		Route::prefix('order_status')->group(function () {
			//Order status list
			Route::get('/','Api\OrderStatusController@index')->name('api.order_status');
			//Show all orders with selected status
			Route::get('/{id}','Api\OrderStatusController@show')->name('status.filter');
			//Add order status
			Route::post('/','Api\OrderStatusController@store');
			//Update order status
			Route::post('/{id}','Api\OrderStatusController@update');
			//Delete order status
			Route::delete('/{id}','Api\OrderStatusController@destroy');
		});

		//Journal categories
		Route::prefix('journal_categories')->group(function () {
			Route::get('/{id}','Api\JournalController@journalCategory');
		});

		//Country, Province/State and City
		Route::prefix('countries')->group(function () {
			Route::get('/','Api\CountryController@index');
			Route::get('/{id}/provinces','Api\ProvinceController@index');
		});
		Route::prefix('provinces')->group(function () {
			Route::get('/{id}/cities','Api\CityController@index');
		});
		
		include_once('jes-other-module-routes.php');

		


	});
});

//public api
Route::get('/v1/customers/{id}/public_wishlists','Api\WishlistController@publicCustomerWishlist');
//Add item in bags
Route::post('/v1/guests/bags','Api\ShoppingBagController@getBagDetailsGuest');
//Get details of each items in shopping bags
Route::post('/v1/guests/bags-multiple','Api\ShoppingBagController@getBagDetailsGuestMultiple');
