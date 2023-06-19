<?php

use Database\Seeders\H5PUpdateCpSemanticsV4Seeder;
use Illuminate\Database\Migrations\Migration;

class H5PUpdateCpSemanticsV4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PUpdateCpSemanticsV4Seeder::class,
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
