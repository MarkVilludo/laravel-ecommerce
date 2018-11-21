<?php

/**
	Super admin routes - User management
**/
//Users
Route::get('users/index', 'SuperAdmin\UserController@index')->name('users.index');
Route::resource('users', 'SuperAdmin\UserController');

//Roles
Route::get('roles/index', 'SuperAdmin\RoleController@index')->name('roles.index');
Route::resource('roles', 'SuperAdmin\RoleController');

//Permissions
Route::get('permissions/index', 'SuperAdmin\PermissionController@index')->name('permissions.index');
Route::resource('permissions', 'SuperAdmin\PermissionController');
//End routes for UM
