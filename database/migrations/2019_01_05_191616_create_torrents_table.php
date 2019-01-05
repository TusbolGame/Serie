<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTorrentsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('torrents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('episode_id')->unsigned()->nullable()->index();
            $table->text('file_name');
            $table->string('magnet_link', '40');
            $table->bigInteger('file_size');
            $table->boolean('used');
            $table->boolean('deleted');
            $table->smallInteger('video_quality_id')->unsigned()->nullable()->index();
            $table->dateTime('started_at');
            $table->dateTime('finished_at');
            $table->dateTime('converted_at');
            $table->timestamps();

            $table->foreign('episode_id')->references('id')->on('episodes')
                ->onUpdate('cascade');
            $table->foreign('video_quality_id')->references('id')->on('video_qualities')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('torrents');
    }
}
