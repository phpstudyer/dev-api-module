<?php

namespace Ly\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * 地址库
 * Class Address
 * @package App\Model
 */
class Address extends Model
{
	/**
	 * @var bool
	 */
	public $timestamps = false;
	/**
	 * 本地动态查询下级列表,默认查询省级
	 * @param $query
	 * @param int $pid
	 * @return mixed
	 */
	public function scopeOfSub($query, $pid=1)
	{
		return $query->where('pid',$pid);
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public static function sub($id)
	{
		return self::where('pid',$id)->get(['name','id']);
	}

	/**
	 * 关联城市
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function city()
	{
		return $this->hasMany(Address::class,'pid','id')->where('level',2);
	}
}
