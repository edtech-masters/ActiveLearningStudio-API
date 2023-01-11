<?php

use Database\Seeders\H5PAddEssayIntoCpSeeder;
use Illuminate\Database\Migrations\Migration;

class H5PAddEssayIntoCp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', [
            '--class' => H5PAddEssayIntoCpSeeder::class,
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
