<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterH5PContentsAddKeywordsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('h5p_contents', function (Blueprint $table) {
            $table->jsonb('content_keywords')->nullable()->default(null);
        });

        Schema::table('h5p_contents', function (Blueprint $table) {
            $table->index('content_keywords', 'idx_content_keywords', 'gin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('h5p_contents', function (Blueprint $table) {
            $table->dropColumn('content_keywords');
        });

        Schema::table('h5p_contents', function (Blueprint $table) {
            $table->dropIndex('idx_content_keywords');
        });


    }
}
