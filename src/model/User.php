<?php

namespace Ly\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class User
 * @package Ly\Api\Model
 */
class User extends Model
{
	use SoftDeletes;
	/**
	 * @var array
	 */
	protected $fillable = ['open_id', 'phone', 'uuid', 'nick_name', 'password', 'img', 'integral', 'qq', 'email', 'sex', 'travel_circle_like_num', 'ip', 'province_id', 'city_id', 'address', 'check_days', 'last_check_at', 'lng', 'lat', 'is_auth', 'auth_status', 'id_card_img'];

	/**
	 * @var array
	 */
	protected $dates = ['last_check_at'];

	/**
	 * 关联省
	 * */
	public function province()
	{
		return $this->belongsTo(Address::class, 'province_id')->select('id', 'name', 'pid');
	}

	/**
	 * 关联区
	 * */
	public function city()
	{
		return $this->belongsTo(Address::class, 'current_city_id')->select('id', 'name', 'pid');
	}

	/**
	 * 查询昵称
	 */
	public function scopeOfKeyword($query, $keyword)
	{
		return $keyword ? $query->where('nickname', 'like', "%$keyword%") : $query;
	}


	/**
	 * @param $query
	 * @param $auth_status
	 * @return mixed
	 */
	public function scopeOfAuth($query, $auth_status)
	{

		$auth_status != null && $query->where('auth_status', $auth_status);

		return $query;

	}
}
