<?php
namespace Ly\Auth\Services;
/**
 * Created by PhpStorm.
 * User: zhaoyadong
 * Date: 17/2/30
 * Time: 下午3:30
 * 短信宝短信接口
 */
class SmsBaoService
{
	public static function sendSms($phone,$content)
	{
		$statusStr = [
			"0" => "短信发送成功",
			"-1" => "参数不全",
			"-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
			"30" => "密码错误",
			"40" => "账号不存在",
			"41" => "余额不足",
			"42" => "帐户已过期",
			"43" => "IP地址限制",
			"50" => "内容含有敏感词"
		];
		$result =file_get_contents("http://api.smsbao.com/"."sms?u=".env('SMS_USERNAME')."&p=".md5(env('SMS_PASSWORD'))."&m=".$phone."&c=".urlencode('[熊猫旅游] '.$content)) ;
		return ['code'=>$result,'msg'=>$statusStr[$result]];
	}
}