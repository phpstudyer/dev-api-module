<?php

namespace  Ly\Api\Controllers;;

use App\Http\Controllers\Controller;
use Ly\Model\Address;
use Illuminate\Http\Request;

/**
 * 地址查询
 * Class AddressController
 * @package App\Http\Controllers\Api
 */
class AddressController extends Controller
{

	/**
	 * 查询下级地址,默认查询省级
	 * @param Request $request
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function getSubCollection(Request $request)
	{
		$list = Address::ofSub($request->get('id', 1))->lists('name', 'id');
		return response($list);
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	public function subList(Request $request)
	{
		$list = Address::ofSub($request->get('id', 1))->select('id','name','pid','is_hot')->get();
		return apiSuccess($list);
	}


	/**
	 * @param Request $request
	 * @return array
	 */
	public function siblingList(Request $request)
	{
		$pid = Address::select('pid')->find($request->get('id', 110000))->pid;
		$list = Address::where('pid',$pid)->select('id','name','pid','is_hot')->get();

		return apiSuccess($list);
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	public function getCityIdByName(Request $request)
	{
		return apiSuccess(Address::where('name',$request->get('city_name'))->first());
	}
}
