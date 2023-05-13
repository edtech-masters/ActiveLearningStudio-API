<?php

use Database\Seeders\H5PUpdateMultipleImageHotspotBehaviourSemanticsV6Seeder;
use Illuminate\Database\Migrations\Migration;

class H5PUpdateMultipleImageHotspotBehaviourSemanticsV6 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PUpdateMultipleImageHotspotBehaviourSemanticsV6Seeder::class,
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
