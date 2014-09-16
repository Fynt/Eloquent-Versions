<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('versions', function($table)
		{
			$table->increments('id');
			$table->integer('object_id');
			// Max table name length is 64 chars.
			$table->string('object_table', 64);
			$table->text('data');
			// SHA1 is 40 chars.
			$table->string('hash', 40);
			$table->timestamps();

			$table->unique(array('object_id', 'object_table', 'hash'));
			$table->index(array('object_id', 'object_table'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('versions');
	}

}
