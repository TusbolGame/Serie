<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('video_views', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->integer('episode_id')->unsigned()->nullable()->index();
            $table->integer('torrent_id')->unsigned()->nullable()->index();
            $table->dateTime('ended_at')->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade');
            $table->foreign('episode_id')->references('id')->on('episodes')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('torrent_id')->references('id')->on('torrents')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('video_views');
    }
}
