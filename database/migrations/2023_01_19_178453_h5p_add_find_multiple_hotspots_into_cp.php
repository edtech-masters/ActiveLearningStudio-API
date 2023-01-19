<?php

use Database\Seeders\H5PAddFindMultipleHotspotsIntoCpSeeder;
use Illuminate\Database\Migrations\Migration;

class H5PAddFindMultipleHotspotsIntoCp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PAddFindMultipleHotspotsIntoCpSeeder::class,
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
