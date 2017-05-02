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
class CreateAddressTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(!Schema::hasTable('addresses')){
			Schema::create('addresses', function (Blueprint $table) {
				$table->bigIncrements('id');
				$table->string('name',100);
				$table->unsignedBigInteger('pid');
				$table->string('pin',100);
				$table->unsignedTinyInteger('level')->comment('1省/直辖市,2市,3区/县,4乡镇');
				$table->unsignedTinyInteger('is_hot')->default(0)->comment('是否是热门城市,1为是');
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
		Schema::dropIfExists('addresses');
	}
}