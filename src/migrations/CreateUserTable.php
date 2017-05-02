<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Created by PhpStorm.
 * User: zhaoyadong
 * Date: 2017/5/2
 * Time: 下午1:21
 */
class CreateUserTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('users')){
			Schema::create('users', function (Blueprint $table) {
				$table->bigIncrements('id');
				$table->string('uuid',50);
				$table->string('open_id',50)->comment('微信openID');
				$table->string('phone',11);
				$table->string('nickname',50);
				$table->string('password',60);
				$table->string('img',100)->comment('头像');
				$table->string('email',50)->default(null);
				$table->unsignedInteger('integral')->default(0);
				$table->string('qq',20);
				$table->unsignedTinyInteger('sex')->default(1)->comment('1男,2女');
				$table->unsignedTinyInteger('state')->default(1)->comment('1正常,0禁用');
				$table->unsignedTinyInteger('service_type')->default(0)->comment('设备类型,0未知,1安卓,2ios');
				$table->string('service_token',50)->comment('设备唯一识别码,推送使用');
				$table->unsignedTinyInteger('is_auth')->default(0)->comment('0失败,1未认证,2认证中,3认证通过');
				$table->string('id_card_img',100)->comment('认证图片');
				$table->string('fail_reason',100)->comment('失败原因');
				$table->unsignedTinyInteger('check_days')->default(0)->comment('用户连续签到天数');
				$table->timestamp('last_check_at');
				$table->unsignedInteger('province_id');
				$table->unsignedInteger('city_id');
				$table->string('address',100);
				$table->string('ip',30);
				$table->string('lng',50);
				$table->string('lat',50);
				$table->softDeletes();
				$table->timestamps();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('users');
	}
}