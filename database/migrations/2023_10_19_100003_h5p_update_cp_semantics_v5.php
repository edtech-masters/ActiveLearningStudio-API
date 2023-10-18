<?php

use Database\Seeders\H5PUpdateCpSemanticsV5Seeder;
use Illuminate\Database\Migrations\Migration;

class H5PUpdateCpSemanticsV5 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PUpdateCpSemanticsV5Seeder::class,
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
