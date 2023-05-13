<?php

use Database\Seeders\H5PUpdateDragDropSemanticsV3Seeder;
use Illuminate\Database\Migrations\Migration;

class H5PUpdateDragDropSemanticsV3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PUpdateDragDropSemanticsV3Seeder::class,
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
