<?php

use Database\Seeders\H5PUpdateFillInTheBlanksSemanticsV2Seeder;
use Illuminate\Database\Migrations\Migration;

class H5PUpdateFillInTheBlanksSemanticsV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PUpdateFillInTheBlanksSemanticsV2Seeder::class,
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
