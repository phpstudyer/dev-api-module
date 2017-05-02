<?php

namespace Ly\Api\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Ly\Model\User;
use Ly\Auth\Services\SmsBaoService;

class AuthController extends Controller
{

	/**
	 * 登录
	 *
	 * @param Request $request
	 * @return array
	 */
	public function postLogin(Request $request)
	{
		try {
			$this->validate($request, [
				'phone' => 'required',
				'password' => 'required',
				'service_type' => 'required',//1android,2ios
				'service_token' => 'required'
			]);
		} catch (\Exception $ex) {
			return apiFail('', $ex->getMessage());
		}
		$phone = $request->get('phone');
		$user = User::where('phone', $phone)->where('state', 1)->first();
		if (!$user) {
			return apiFail([], '帐号不存在或被禁用');
		}
		if (password_verify($request->get('password', ''), $user->password)) {
			$data = ['last_login_ip' => $request->ip(), 'last_login_at' => Carbon::now(), 'service_type' => $request->get('service_type')];
			if ($request->get('service_token')) {
				$data['service_token'] = $request->get('service_token');
			}
			if ($user->last_check_at && $user->last_check_at->startOfDay()->diffInDays() > 1) {
				$data['check_days'] = 0;
			}
			$user->fill($data)->save();

			return apiSuccess(['token' => RC4Encrypt(['id' => $user->id, 'phone' => $user->phone, 'service_type' => $user->service_type, 'service_token' => $user->service_token]), 'user' => $user], '', '登录成功');
		}
		return apiFail('', '信息填写有误,请检查后重试');
	}

	/**
	 * 注册
	 * @param Request $request
	 * @return array
	 */
	public function postRegister(Request $request)
	{
		try {
			$this->validate($request, [
				'sms_token' => 'required|min:1',
				'sms_code' => 'required|size:6',
				'phone' => 'required',
				'password' => 'required|min:6|max:16',
				'service_type' => 'required',
				'service_token' => 'required'
			]);
		} catch (\Exception $ex) {
			return apiFail('', $ex->getMessage());
		}

		$data = RC4Decrypt($request->get('sms_token'));

		if ($data['phone'] != $request->get('phone')) {
			return apiFail([], '手机号码有变动,请重新发送验证');
		}
		if ($data['sms_code'] != $request->get('sms_code')) {
			return apiFail([], '验证码错误');
		}
		//检测是否已注册
		if (User::where('phone', $request->get('phone'))->first()) {
			return apiFail('', '帐号已存在');
		}
		//开始注册
		$data['phone'] = $request->get('phone');
		$data['uuid'] = Uuid::uuid();
		$data['nick_name'] = $data['phone'];
		$data['img'] = '/images/config/user_img@' . rand(1, 3) . '.png';
		$data['password'] = password_hash($request->get('password'), PASSWORD_DEFAULT);
		//设备类型,1安卓,2IOS
		$data['service_type'] = $request->get('service_type');
		//设备号
		$data['service_token'] = $request->get('service_token');
		$data['ip'] = $request->ip();
		$user = User::create($data);
		$token = RC4Encrypt(['id' => $user->id, 'phone' => $user->phone, 'service_type' => $user->service_type, 'service_token' => $user->service_token]);
		return $user ? apiSuccess(['user' => $user, 'token' => $token, 'first_login' => true], '', '注册成功') : apiFail('', '网络通信故障,请重新注册');
	}

	/**
	 * 获取短信验证码
	 *
	 * @param Request $request
	 * @return array
	 */
	public function getCode(Request $request)
	{
		try {
			$this->validate($request, ['phone' => 'required|digits:11']);
		} catch (\Exception $ex) {
			return apiFail('', $ex->getMessage());
		}

		$phone = $request->get('phone');
		if (Cache::get('sms_code:' . $phone)) {
			return success([], '', '短信发送成功,3分钟内有效');
		}
		$code = rand(100000, 999999);
		$result = SmsBaoService::sendSms($phone, "您的验证码是：{$code}。请不要把验证码泄露给其他人。");
		if (!$result['code']) {
			$data['sms_code'] = $code;
			$data['sms_token'] = RC4Encrypt(['phone' => $phone, 'sms_code' => $code]);
			Cache::put('sms_code:' . $phone, $code, 3);
			return apiSuccess($data, '', '短信发送成功');
		}
		return apiFail([], '短信发送失败,请稍后重试');
	}

	/**
	 * 忘记密码
	 *
	 * @param Request $request
	 * @return array
	 */
	public function postChangePassword(Request $request)
	{
		try {
			$this->validate($request, [
				'sms_token' => 'required',
				'sms_code' => 'required',
				'phone' => 'required',
				'password' => 'required|min:6|max:16',
				'service_type' => 'required',
				'service_token' => 'required'
			]);
		} catch (\Exception $ex) {
			return apiFail('', $ex->getMessage());
		}
		$data = RC4Decrypt($request->get('sms_token'));
		if ($data['phone'] != $request->get('phone')) {
			return apiFail([], '手机号码有变动,请重新发送验证');
		}
		if ($data['sms_code'] != $request->get('sms_code')) {
			return apiFail([], '验证码错误');
		}
		//检测是否已注册
		$user = User::where('phone', $request->get('phone'))->first();
		if (!$user) {
			return apiFail('', '帐号尚未注册,请前往注册');
		}
		//开始注册
		$data['phone'] = $request->get('phone');
		$data['password'] = password_hash($request->get('password'), PASSWORD_DEFAULT);
		//设备类型,1安卓,2IOS
		$data['service_type'] = $request->get('service_type');
		//设备号
		$data['service_token'] = $request->get('service_token');
		$data['last_login_ip'] = $request->ip();
		$data['last_login_at'] = Carbon::now();
		$user->fill($data)->save();
		$token = RC4Encrypt(['id' => $user->id, 'phone' => $user->phone, 'service_type' => $data['service_type'], 'service_token' => $data['service_token']]);
		return $user ? apiSuccess(['user' => $user, 'token' => $token], '', '修改成功') : apiFail('', '网络通信故障,请重新操作');
	}
}
