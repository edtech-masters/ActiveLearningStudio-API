<?php

use Database\Seeders\H5PAddMemoryGameToCpSemanticsSeeder;
use Illuminate\Database\Migrations\Migration;

class H5PAddMemoryGameToCpSemanticsV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PAddMemoryGameToCpSemanticsSeeder::class,
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
