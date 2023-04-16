<?php

use Database\Seeders\H5PUpdateMultipleImageHotspotBehaviourSemanticsV4Seeder;
use Illuminate\Database\Migrations\Migration;

class H5PUpdateMultipleImageHotspotBehaviourSemanticsV4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PUpdateMultipleImageHotspotBehaviourSemanticsV4Seeder::class,
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
