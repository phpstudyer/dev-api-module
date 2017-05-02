<?php
/**
 * Created by PhpStorm.
 * User: zhaoyadong
 * Date: 2017/2/4
 * Time: 下午5:33
 */
if (!function_exists("RC4Encrypt")) {
	/**
	 * @param $data  array 明文
	 * @return string
	 */
	function RC4Encrypt(array $data)
	{
		if (is_array($data)) {
			$data = json_encode($data);
		}
		$key = env('APP_KEY');
		$pwd = md5(md5($key) . $key);
		//因为RC4是二进制加密算法，所以密文是无法直接当作文本查看
		return base64_encode(RC4($pwd, $data));
	}
}
if (!function_exists("RC4Decrypt")) {
	/**
	 * 解密
	 * @param $cipher string 密文
	 * @return array
	 */
	function RC4Decrypt($cipher)
	{
		$key = env('APP_KEY');
		$pwd = md5(md5($key) . $key);
		return json_decode(RC4($pwd, base64_decode($cipher)), true);
	}
}

if (!function_exists("RC4")) {
	/**
	 * 加密算法
	 * @param $pwd
	 * @param $data
	 * @return string
	 */
	function RC4($pwd, $data)
	{
		$key[] = "";
		$box[] = "";
		$cipher = '';

		$pwd_length = strlen($pwd);
		$data_length = strlen($data);

		for ($i = 0; $i < 256; $i++) {
			$key[$i] = ord($pwd[$i % $pwd_length]);
			$box[$i] = $i;
		}

		for ($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $key[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for ($a = $j = $i = 0; $i < $data_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;

			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;

			$k = $box[(($box[$a] + $box[$j]) % 256)];
			$cipher .= chr(ord($data[$i]) ^ $k);
		}

		return $cipher;
	}
}
if (!function_exists('success')) {
	/**
	 * @param mixed $data
	 * @param string $url
	 * @param string $msg
	 * @param int $status
	 * @return array
	 */
	function success($data = null, $url = '', $msg = '操作成功', $status = 1)
	{
		return ['msg' => $msg, 'status' => $status, 'url' => $url, 'data' => $data];
	}
}

if (!function_exists('apiSuccess')) {
	/**
	 * @param mixed $data
	 * @param string $url
	 * @param string $msg
	 * @param int $status
	 * @return array
	 */
	function apiSuccess($data = null, $msg = '操作成功', $url = '', $status = 1)
	{
		return response(['msg' => $msg, 'status' => $status, 'url' => $url, 'data' => $data]);
	}
}

if (!function_exists('fail')) {
	/**
	 * @param array $data
	 * @param string $msg
	 * @param int $status
	 * @return array
	 */
	function fail($data = [], $msg = '操作失败', $status = 0)
	{
		return ['msg' => $msg, 'status' => $status, 'data' => $data];
	}
}
if (!function_exists('apiFail')) {
	/**
	 * @param array $data
	 * @param string $msg
	 * @param int $status
	 * @return array
	 */
	function apiFail($data = [], $msg = '操作失败', $status = 0)
	{
		return response(['msg' => $msg, 'status' => $status, 'data' => $data]);
	}
}

if (!file_exists('backWithError')) {
	/**
	 * @param $errorMsg
	 * @return $this
	 */
	function backWithError($errorMsg)
	{
		return redirect()->back()->withErrors($errorMsg)->withInput();
	}
}
if (!function_exists('upload_url')) {

	/**
	 * 获取上传目录URL
	 *
	 * @param string|null $path
	 * @param string $type
	 * @param bool $secure
	 * @return string
	 */
	function upload_url($path = null, $type = '', $secure = null)
	{
		$configName = 'path.upload';
		$type && $configName .= '_' . $type;

		$relatePath = str_replace(public_path(), '', config($configName));
		return str_replace('\\', '/', asset($relatePath . $path, $secure));
	}
}
if (!function_exists('getRealIp')) {
	/**
	 * @return mixed
	 */
	function getRealIp()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip is pass from proxy
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}

if (!function_exists('getMenuTree')) {
	/**
	 * @param array $menu
	 * @param array $selected
	 * @param string $field
	 */
	function getMenuTree($menu = [], $selected = [], $field = 'id')
	{
		foreach ($menu as $key => $value) {
			echo '<li id="' . $value [$field] . '" ' . (!empty ($selected)
				&& in_array($value ['id'], $selected) ? 'class="selected"' : "") . '>' . $value ['name'];
			if (isset ($value ['child']) && is_array($value ['child'])) {
				echo '<ul>';
				getMenuTree($value ['child'], $selected, $field);
				echo '</ul></li>';
			} else {
				echo '</li>';
			}
		}
	}
}

if (!function_exists('toTree')) {
	/**
	 * @param $data
	 * @param int $pid
	 * @param string $child
	 * @return array
	 */
	function toTree($data, $pid = 0, $child = 'child')
	{
		$tree = [];
		foreach ($data as $item) {
			if (isset($data[$item['pid']])) {
				$data[$item['pid']][$child][] = &$data[$item['id']];
			} else {
				$tree[] = &$data[$item['id']];
			}

		}
		return $tree;
	}
}

if (!function_exists('humansTime')) {
	/**
	 * @param int $time
	 * @return string
	 */
	function humansTime($time = 0)
	{
		$parse = $time ? Carbon\Carbon::parse($time) : Carbon\Carbon::now();
		$hour = $parse->hour;
		if ($hour < 3) {
			$str = '深夜';
		} else if ($hour < 5) {
			$str = '凌晨';
		} else if ($hour < 8) {
			$str = '清晨';
		} else if ($hour < 12) {
			$str = '上午';
		} else if ($hour < 14) {
			$str = '中午';
		} else if ($hour < 18) {
			$str = '下午';
		} else if ($hour < 19) {
			$str = '傍晚';
		} else if ($hour < 23) {
			$str = '晚上';
		} else {
			$str = '深夜';
		}
		return $str;
	}
}

if (!function_exists('getOrderNo')) {
	/**
	 * 随机生成订单号
	 * @return string
	 */
	function getOrderNo()
	{
		list($_, $mic) = explode(' ', microtime());
		return 'XM' . date('Ymd') . substr($mic, -4) . rand(10, 99);
	}
}

if (!function_exists('getAction')) {
	/**
	 * 获取访问的模块
	 * @return string
	 */
	function getAction()
	{
		$pattern = trim($_SERVER['REQUEST_URI'], '/');
		return $pattern == '' ? 'index' : explode('/', $pattern)[1];
	}
}

if (!function_exists('getCurrentAction')) {
	/**
	 * 当前控制器信息
	 * @return array
	 */
	function getCurrentAction()
	{
		$action = \Illuminate\Support\Facades\Route::current()->getActionName();
		list($class, $method) = explode('@', $action);
		$action = substr($class, strrpos($class, '\\') + 1);
		return ['action' => $action, 'method' => $method];
	}
}

if (!function_exists('ip2address')) {
	/**
	 * @param $ip
	 * @return string
	 */
	function ip2address($ip)
	{
		$info = file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
		$json = explode('=', $info);
		$info = json_decode(substr($json[1], 0, -1), true);
		return $info['province'] . $info['city'];
	}
}

if (!function_exists('getUploadField')) {
	/**
	 * @param string $fieldName
	 * @param string $msg
	 * @param string $dataType
	 * @param string $buttonClass
	 * @param string $fieldValue
	 */
	function getUploadField($fieldName = 'img', $msg = '图片', $dataType = 'img', $buttonClass = 'info', $fieldValue = '')
	{
		$multiFileName = $fieldName;
		if (in_array($dataType, ['multi-img', 'multi-file'])) {
			$multiFileName .= '[]';
		}
		$addStr = '';
		$input = $dataType == 'img' || $dataType == 'file' ? "<input type=hidden id=$fieldName name=$multiFileName value=$fieldValue>" : '';
		if ($fieldValue) {
			if ($dataType == 'file') {
				$addStr = '<p style="font-size: 18px">' . pathinfo($fieldValue)['basename'] . '</p>';
			} elseif ($dataType == 'multi-file') {
				$fieldName .= '[]';
				foreach ($fieldValue as $v) {
					if (isset($v['img'])) {
						$path = $v['img'];
					} elseif (isset($v['path'])) {
						$path = $v['path'];
					}
					$addStr .= "<div><p style=\"font-size: 18px\">{pathinfo($path)['basename']}<input type=\"hidden\" name=\"{$fieldName}[]\" value=\"$v\" /></p><a  class=\"btn btn-danger file-delete\">删除</a></div>";
				}
			} elseif ($dataType == 'multi-img') {
				$fieldName .= '[]';
				foreach ($fieldValue as $v) {
					if (isset($v['img'])) {
						$path = $v['img'];
					} elseif (isset($v['path'])) {
						$path = $v['path'];
					}
					$addStr .= "<div><img src=\"$path\" /><i class=\"fa fa-trash image-delete\" style=\"position: relative;top: 0;right: 0;\">删除</i><input type=\"hidden\" name=\"{$fieldName}[]\" value=\"$path\" /></div>";
				}
			} else {
				$addStr = '<img src="' . $fieldValue . '" />';
			}
		}
		echo <<<EOF
		<div class="form-group">
			<label class="control-label">
			{$msg} <span class="symbol required"></span>
			</label>
			{$input}
			<a class="btn btn-{$buttonClass} file-upload" data-type={$dataType} data-name={$multiFileName}>上传{$msg}</a>
			<div class="show-img">
			{$addStr}
			</div>
		</div>
EOF;
	}
}

if (!function_exists('getTextField')) {
	/**
	 * @param string $fieldName
	 * @param string $msg
	 * @param string $filedType
	 * @param string $fieldValue
	 */
	function getTextField($fieldName = 'name', $msg = '姓名', $filedType = 'text', $fieldValue = '')
	{
		echo <<<EOF
		<div class="form-group">
                 <label class="control-label">
                  {$msg} <span class="symbol required"></span>
                 </label>
                 <input type="{$filedType}" placeholder="请填写{$msg}" class="form-control" id="{$fieldName}" name="{$fieldName}" value="{$fieldValue}">
        </div>
EOF;
	}
}


if (!function_exists('getTextareaField')) {
	/**
	 * @param string $fieldName
	 * @param string $msg
	 * @param boolean $isRich
	 * @param string $fieldValue
	 */
	function getTextareaField($fieldName = 'content', $msg = '内容', $isRich = false, $fieldValue = '')
	{
		$id = $isRich ? 'text-container' : $fieldName;
		$class = $isRich ? '' : 'class=form-control';
		echo <<<EOF
		<div class="form-group">
	        <label class="control-label">
	            {$msg} <span class="symbol required"></span>
	        </label>
	        <textarea name="{$fieldName}" id="{$id}" {$class} placeholder="请填写{$msg}">{$fieldValue}</textarea>
	    </div>
EOF;
	}
}


if (!function_exists('getRadioField')) {
	/**
	 * @param string $fieldName
	 * @param string $msg
	 * @param array $fieldCollect
	 * @param string $fieldValue
	 */
	function getRadioField($fieldName = 'name', $msg = '姓名', $fieldCollect = [], $fieldValue = '')
	{
		$collect = '';
		foreach ($fieldCollect as $k => $v) {
			$collect .= '<input type="radio"  id="' . $fieldName . '" name="' . $fieldName . '" value="' . $k . '"';
			$collect .= ($k == $fieldValue || !$k ? "checked" : "") . ' />' . $v;
		}

		echo <<<EOF
		<div class="form-group">
	        <label class="control-label">
	            {$msg} <span class="symbol required"></span>
	        </label>
	        <div class="form-controls">
	            {$collect}
	        </div>
        </div>
EOF;

	}
}

if (!function_exists('formatDate')) {
	/**
	 * @param $dateTimeString
	 * @return string
	 */
	function formatDate($dateTimeString)
	{
		return Carbon\Carbon::parse($dateTimeString)->toDateString();
	}
}

if (!function_exists('formatDateTime')) {
	/**
	 * @param $dateTimeString
	 * @return string
	 */
	function formatDateTime($dateTimeString)
	{
		return Carbon\Carbon::parse($dateTimeString)->toDateTimeString();
	}
}