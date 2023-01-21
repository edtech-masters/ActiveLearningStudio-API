<?php

use Database\Seeders\H5PUpdateFindMultipleHotspotsSeeder;
use Illuminate\Database\Migrations\Migration;

class H5PUpdateFindMultipleHotspots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PUpdateFindMultipleHotspotsSeeder::class,
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
