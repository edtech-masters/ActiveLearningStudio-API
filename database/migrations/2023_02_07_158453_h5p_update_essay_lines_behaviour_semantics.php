<?php

use Database\Seeders\H5PUpdateEssayLinesBehaviourSemanticsSeeder;
use Illuminate\Database\Migrations\Migration;

class H5PUpdateEssayLinesBehaviourSemantics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PUpdateEssayLinesBehaviourSemanticsSeeder::class,
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
