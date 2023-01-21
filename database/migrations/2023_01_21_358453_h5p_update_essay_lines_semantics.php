<?php

use Database\Seeders\H5PUpdateEssayLinesSemanticsSeeder;
use Illuminate\Database\Migrations\Migration;

class H5PUpdateEssayLinesSemantics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PUpdateEssayLinesSemanticsSeeder::class,
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
