<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookmarksTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('video_view_id')->unsigned()->nullable()->index();
            $table->integer('bookmark_type_id')->nullable()->unsigned()->index();
            $table->time('time')->index();
            $table->timestamps();

            $table->foreign('video_view_id')->references('id')->on('video_views')
                ->onUpdate('cascade');
            $table->foreign('bookmark_type_id')->references('id')->on('bookmark_types')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('bookmarks');
    }
}
