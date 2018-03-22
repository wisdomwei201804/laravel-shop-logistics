<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShopLogisticsTplTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shop_logistics_tpl', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->char('name', 32)->nullable()->comment('模板名称');
			$table->string('regexp', 512)->nullable()->comment('规则');
			$table->timestamps();
			$table->softDeletes()->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shop_logistics_tpl');
	}

}
