<?php

use Database\Seeders\H5PUpdateCpSemanticsV3Seeder;
use Illuminate\Database\Migrations\Migration;

class H5PUpdateCpSemanticsV3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PUpdateCpSemanticsV3Seeder::class,
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
