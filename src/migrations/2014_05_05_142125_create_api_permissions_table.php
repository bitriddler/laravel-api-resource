<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('api_permissions', function(Blueprint $table)
		{
			$table->increments('id');

            $table->string('resource');
            $table->string('action');

            $table->integer('application_id')->unsigned();
            $table->foreign('application_id')->references('id')->on('api_applications')->onDelete('CASCADE')->onUpdate('CASCADE');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('api_permissions');
	}

}
