<?php

use Database\Seeders\H5PUpdateMultipleImageHotspotBehaviourSemanticsSeeder;
use Illuminate\Database\Migrations\Migration;

class H5PUpdateMultipleImageHotspotBehaviourSemantics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PUpdateMultipleImageHotspotBehaviourSemanticsSeeder::class,
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
