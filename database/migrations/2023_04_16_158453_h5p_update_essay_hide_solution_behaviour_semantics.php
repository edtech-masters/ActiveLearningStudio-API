<?php

use Database\Seeders\H5PUpdateEssayHideSolutionBehaviourSemanticsSeeder;
use Illuminate\Database\Migrations\Migration;

class H5PUpdateEssayHideSolutionBehaviourSemantics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PUpdateEssayHideSolutionBehaviourSemanticsSeeder::class,
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
