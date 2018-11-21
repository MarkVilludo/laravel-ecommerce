<?php

/**
	OTHER MODULES
	- JES
**/


	// Vouchers, Discounts, Coupons
	Route::prefix('vouchers')->group(function () {
		// get all vouchers
		Route::get('/','Api\VoucherController@index')->name('api.vouchers');
		// store newly created voucher
		// Route::post('/','Api\VoucherController@store')->name('api.vouchers.store');
		// search for a voucher
		Route::post('/validate','Api\VoucherController@validateCode')->name('api.vouchers.validate');

		// get specific voucher details
		Route::get('/{id}','Api\VoucherController@show');
		// update specific voucher details
		// Route::post('/{id}','Api\VoucherController@update');
		// delete a voucher
		// Route::delete('/{id}','Api\VoucherController@destroy');

		//use voucher
		Route::post('/get_discount','Api\VoucherController@getVoucherDiscount')->name('api.vouchers.get_siscount');



});
