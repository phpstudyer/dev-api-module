<?php

/**
 * Created by PhpStorm.
 * User: zhaoyadong
 * Date: 2017/5/2
 * Time: 下午1:14
 */
Route::group(['prefix' => 'api','namespace'=>'Ly\Api\Controllers'], function ($router) {
	$router->group(['prefix'=>'auth'],function ($router){
		$router->post('login', 'AuthController@postLogin');
		$router->post('register', 'AuthController@postRegister');
		$router->get('code', 'AuthController@getCode');
		$router->post('change-password', 'AuthController@postChangePassword');
	});

	$router->post('file/upload', 'FileController@postUploadTemp');//上传图片
	$router->get('address-sub-unit', 'AddressController@subList');//获取下级地址
	$router->get('address-sibling-unit', 'AddressController@siblingList');//获取平级级地址
	$router->get('address-city-id', 'AddressController@getCityIdByName');//获取省市 ID
});
