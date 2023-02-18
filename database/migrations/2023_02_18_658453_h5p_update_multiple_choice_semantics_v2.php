<?php

use Database\Seeders\H5PUpdateMultipleChoiceSemanticsV2Seeder;
use Illuminate\Database\Migrations\Migration;

class H5PUpdateMultipleChoiceSemanticsV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PUpdateMultipleChoiceSemanticsV2Seeder::class,
            '--force' => true
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
